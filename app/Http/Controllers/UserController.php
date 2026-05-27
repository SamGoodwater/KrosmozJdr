<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\OAuthAccount;
use App\Models\User;
use App\Services\Media\EntityImageMediaService;
use App\Services\NotificationService;
use App\Support\OAuthConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contrôleur de gestion des utilisateurs.
 *
 * Deux modes de gestion de l'avatar sont possibles :
 * - via les méthodes store/update (profil complet)
 * - via les endpoints dédiés updateAvatar/deleteAvatar (modification rapide de l'avatar)
 *
 * Cela permet de couvrir à la fois les formulaires classiques et les UX modernes (upload instantané).
 */
class UserController extends Controller
{
    public function __construct(
        private EntityImageMediaService $entityImageMediaService,
    ) {}

    /**
     * Affiche la liste paginée des utilisateurs.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->input('search', ''));
        $role = $request->input('role');
        $status = (string) $request->input('status', 'all'); // active|trashed|all

        $query = User::query()->with(['scenarios', 'campaigns', 'pages', 'sections', 'media']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (is_numeric($role) && array_key_exists((int) $role, User::ROLES)) {
            $query->where('role', (int) $role);
        }

        if ($status === 'trashed') {
            $query->onlyTrashed();
        } elseif ($status === 'all') {
            $query->withTrashed();
        }

        $users = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Pages/user/Index', [
            'users' => UserResource::collection($users),
            'filters' => [
                'search' => $search,
                'role' => is_numeric($role) ? (int) $role : null,
                'status' => $status,
            ],
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Affiche le détail d'un utilisateur.
     * Si aucun utilisateur n'est spécifié, affiche le profil de l'utilisateur connecté.
     *
     * @return Response
     */
    public function show(?User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('view', $user);
        $user->load(['scenarios', 'campaigns', 'pages', 'sections']);

        return Inertia::render('Pages/user/Show', [
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Confirme le mot de passe de l'utilisateur (mode modal/API).
     * Utilisé par ConfirmPasswordModal pour protéger les actions sensibles.
     *
     * @return JsonResponse
     */
    public function confirmPassword(Request $request)
    {
        $this->authorize('update', $request->user());

        $request->validate(['password' => ['required', 'string']]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                'errors' => ['password' => [__('auth.password')]],
            ], 422);
        }

        $now = time();
        $request->session()->put('auth.password_confirmed_at', $now);
        $request->session()->put('auth.password_last_activity_at', $now);

        return response()->json(['confirmed' => true]);
    }

    /**
     * Affiche le formulaire de création d'utilisateur.
     *
     * @return Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        return Inertia::render('Pages/user/Create', [
            'roles' => User::ROLES,
            'notificationChannels' => User::NOTIFICATION_CHANNELS,
        ]);
    }

    /**
     * Crée un nouvel utilisateur (profil complet, avatar inclus si fourni).
     *
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $data = $request->validated();

        if (($data['role'] ?? User::ROLE_USER) === User::ROLE_SUPER_ADMIN) {
            return back()->withErrors(['role' => 'Impossible de créer directement un super administrateur.']);
        }

        if (! isset($data['role']) || ! array_key_exists((int) $data['role'], User::ROLES)) {
            $data['role'] = User::ROLE_USER;
        }

        if ($request->hasFile('avatar')) {
            unset($data['avatar']);
        }
        // Champ de validation uniquement ; avec Model::unguard() global, il serait sinon envoyé en SQL.
        unset($data['password_confirmation']);
        $data['notifications_enabled'] = $data['notifications_enabled'] ?? true;
        $data['notification_channels'] = $data['notification_channels'] ?? ['database'];
        $user = User::create($data);
        if ($request->hasFile('avatar')) {
            $this->entityImageMediaService->attachFromRequest($user, $request, 'avatar', 'avatars', 'avatar');
        }

        return redirect()->route('user.admin.edit', $user)->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'utilisateur.
     * Si aucun utilisateur n'est spécifié, affiche le formulaire pour l'utilisateur connecté.
     *
     * @return Response
     */
    public function edit(?User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);
        $user->load(['scenarios', 'campaigns', 'pages', 'sections']);

        return Inertia::render('Pages/user/Edit', [
            'user' => new UserResource($user),
            'roles' => User::ROLES,
            'notificationChannels' => User::NOTIFICATION_CHANNELS,
            'notificationTypes' => config('notifications.types', []),
            'notificationChannelsLabels' => config('notifications.channels', []),
            'notificationFrequencies' => config('notifications.frequencies', []),
        ]);
    }

    /**
     * Affiche la page des paramètres du compte (onglets : notifications, etc.).
     * Profil courant uniquement.
     *
     * @return Response
     */
    public function settings()
    {
        $user = Auth::user();
        $this->authorize('update', $user);
        $user->load(['oauthAccounts']);

        return Inertia::render('Pages/user/Settings', [
            'user' => (new UserResource($user))->toArray(request()),
            'oauthProviders' => OAuthConfig::enabledProviders(),
            'notificationTypes' => config('notifications.types', []),
            'notificationChannelsLabels' => config('notifications.channels', []),
            'notificationFrequencies' => config('notifications.frequencies', []),
        ]);
    }

    /**
     * Met à jour un utilisateur (profil courant ou admin).
     *
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, ?User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);
        $old = clone $user;
        $data = $request->validated();

        if (! $request->has('notification_channels')) {
            unset($data['notification_channels']);
        }

        // Gestion de l'avatar (Media Library)
        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatars');
            $this->entityImageMediaService->attachFromRequest($user, $request, 'avatar', 'avatars', 'avatar');
            unset($data['avatar']);
        }
        // Normalisation des préférences de notifications : forme { channels: [], frequency: 'instant'|... }
        // Utiliser input() car validated() peut exclure des clés imbriquées avec règles sometimes
        if ($request->has('notification_preferences')) {
            $allowedTypes = array_keys(config('notifications.types', []));
            $prefs = $request->input('notification_preferences', []);
            if (is_array($prefs)) {
                $data['notification_preferences'] = [];
                foreach (array_intersect_key($prefs, array_flip($allowedTypes)) as $type => $val) {
                    $channels = [];
                    $frequency = config('notifications.types.'.$type.'.frequency_default', 'instant');
                    if (is_array($val)) {
                        if (isset($val['channels']) && is_array($val['channels'])) {
                            $channels = array_values(array_intersect($val['channels'], ['database', 'mail']));
                            $frequency = in_array($val['frequency'] ?? '', ['instant', 'daily', 'weekly', 'monthly'], true)
                                ? $val['frequency'] : $frequency;
                        } else {
                            // Format legacy : valeur = tableau de canaux uniquement
                            $channels = array_values(array_intersect($val, ['database', 'mail']));
                        }
                    }
                    $data['notification_preferences'][$type] = ['channels' => $channels, 'frequency' => $frequency];
                }
            }
        }

        $user->update($data);

        if ($this->shouldNotifyProfileModified($request)) {
            try {
                NotificationService::notifyProfileModified($user, Auth::user(), $old);
            } catch (\Throwable $e) {
                report($e);
            }
        }
        // Redirection selon le contexte ou la demande (ex. depuis la page paramètres)
        if ($request->input('redirect') === 'settings' && $user->id === Auth::id()) {
            if ($request->header('X-Inertia')) {
                return back()->with('success', 'Préférences enregistrées.');
            }

            return redirect()->to(route('user.settings').'#notifications')->with('success', 'Préférences enregistrées.');
        }
        if ($user->id === Auth::id()) {
            return redirect()->route('user.show', $user)->with('success', 'Profil mis à jour.');
        }

        return redirect()->route('user.admin.edit', $user)->with('success', 'Utilisateur mis à jour.');
    }

    /**
     * Supprime (soft delete) un utilisateur.
     *
     * @return RedirectResponse
     */
    public function delete(?User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('delete', $user);

        if ($user->id === Auth::id()) {
            return back()->withErrors([
                'user' => 'Utilise la page Confidentialité pour supprimer ton propre compte.',
            ]);
        }

        if ($user->trashed()) {
            return back()->with('info', 'Ce compte est déjà archivé.');
        }

        try {
            NotificationService::notifyUserDeleted($user, Auth::user());
        } catch (\Throwable $e) {
            report($e);
        }
        $user->delete();

        return redirect()->route('user.index', ['status' => 'all'])->with('success', 'Utilisateur archivé.');
    }

    /**
     * Supprime définitivement un utilisateur (admin only).
     * Supprime aussi l'avatar physique si présent.
     */
    public function forceDelete(User $user): RedirectResponse
    {
        $this->authorize('forceDelete', $user);
        $user->clearMediaCollection('avatars');
        $user->forceDelete();

        return redirect()->route('user.index')->with('success', 'Utilisateur supprimé définitivement.');
    }

    /**
     * Restaure un utilisateur supprimé.
     */
    public function restore(User $user): RedirectResponse
    {
        $this->authorize('restore', $user);
        $user->restore();

        return redirect()->route('user.index')->with('success', 'Utilisateur restauré.');
    }

    /**
     * Met à jour uniquement l'avatar de l'utilisateur (endpoint dédié, UX moderne).
     * Si aucun utilisateur n'est spécifié, utilise l'utilisateur connecté.
     *
     * @return RedirectResponse
     */
    public function updateAvatar(Request $request, ?User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);

        // Vérifier que le fichier est présent
        if (! $request->hasFile('avatar')) {
            return redirect()->back()->withErrors(['avatar' => 'Aucun fichier n\'a été téléchargé.']);
        }

        $user->clearMediaCollection('avatars');
        $this->entityImageMediaService->attachFromRequest($user, $request, 'avatar', 'avatars', 'avatar');

        // Recharger l'utilisateur avec les relations pour retourner les données complètes
        $user->refresh();

        return redirect()->back()->with('success', 'Avatar mis à jour.');
    }

    /**
     * Supprime uniquement l'avatar de l'utilisateur (endpoint dédié, UX moderne).
     * Si aucun utilisateur n'est spécifié, utilise l'utilisateur connecté.
     *
     * @return RedirectResponse
     */
    public function deleteAvatar(?User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);
        $user->clearMediaCollection('avatars');
        $user->update(['avatar' => null]);

        return redirect()->back()->with('success', 'Avatar supprimé.');
    }

    /**
     * Met à jour le rôle d'un utilisateur (seul le super_admin peut promouvoir en admin, personne ne peut promouvoir en super_admin).
     *
     * @return RedirectResponse
     */
    public function updateRole(Request $request, User $user)
    {
        $this->authorize('updateRole', $user);

        $request->validate([
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        // Convertir le rôle en entier si c'est une string (pour compatibilité)
        $roleValue = is_numeric($request->input('role'))
            ? (int) $request->input('role')
            : array_search($request->input('role'), User::ROLES, true);

        if ($roleValue === false) {
            return back()->withErrors(['role' => 'Rôle invalide.']);
        }

        // Interdit de promouvoir en super_admin
        if ($roleValue === User::ROLE_SUPER_ADMIN) { // super_admin = 5
            return back()->withErrors(['role' => 'Impossible de promouvoir un utilisateur en super_admin.']);
        }

        // Seul le super_admin peut promouvoir en admin
        if ($roleValue === User::ROLE_ADMIN && ! $request->user()->isInteractiveSuperAdmin()) { // admin = 4
            return back()->withErrors(['role' => 'Seul le super_admin peut promouvoir un utilisateur en admin.']);
        }

        $user->role = $roleValue;
        $user->save();

        return redirect()->back()->with('success', 'Rôle mis à jour.');
    }

    /**
     * Met à jour le mot de passe de l'utilisateur.
     * - Si l'utilisateur modifie son propre mot de passe : current_password requis
     * - Si un admin modifie le mot de passe d'un autre utilisateur : current_password non requis
     *
     * @param  User|null  $user  Utilisateur dont on modifie le mot de passe (null = utilisateur connecté)
     * @return RedirectResponse
     */
    public function updatePassword(Request $request, ?User $user = null)
    {
        $user = $user ?? Auth::user();
        $isSelfUpdate = $user->id === Auth::id();

        if ($isSelfUpdate) {
            $this->authorize('update', $user);
        } else {
            $this->authorize('resetPassword', $user);
        }

        $rules = [
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        // En mise à jour de son propre mot de passe : current_password requis sauf si compte OAuth-only (pas de mdp).
        if ($isSelfUpdate && $user->hasPassword()) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'password' => \Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe mis à jour.');
    }

    /**
     * Convertit un compte OAuth-only en compte classique (ajout d'un mot de passe).
     *
     * @return RedirectResponse
     */
    public function convertToClassicAccount(Request $request)
    {
        $user = Auth::user();
        $this->authorize('update', $user);

        if ($user->hasPassword()) {
            return back()->with('info', 'Ton compte possède déjà un mot de passe.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.settings')->with('success', 'Compte converti. Tu peux maintenant te connecter avec ton email et ton mot de passe.');
    }

    /**
     * Délie un provider OAuth du compte (si au moins une autre méthode de connexion reste).
     *
     * @return RedirectResponse
     */
    public function unlinkOAuthProvider(string $provider)
    {
        $user = Auth::user();
        $this->authorize('update', $user);

        if (! OAuthConfig::isProviderEnabled($provider) || ! in_array($provider, OAuthAccount::PROVIDERS, true)) {
            return back()->withErrors(['provider' => 'Provider invalide ou non configuré.']);
        }

        if (! $user->canUnlinkProvider($provider)) {
            return back()->with('error', 'Impossible de délier ce compte : tu dois conserver au moins une méthode de connexion (mot de passe ou autre provider).');
        }

        $user->oauthAccounts()->provider($provider)->delete();

        return back()->with('success', 'Compte '.$provider.' délié.');
    }

    /**
     * Notifie la modification de profil uniquement si des champs identité ont changé
     * (pas lors d'une mise à jour des seules préférences de notification).
     */
    private function shouldNotifyProfileModified(UpdateUserRequest $request): bool
    {
        return $request->hasAny(['name', 'email', 'password'])
            || $request->hasFile('avatar');
    }
}
