<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Http\Resources\UserResource;
use App\Services\NotificationService;

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
    /**
     * Affiche la liste paginée des utilisateurs.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->input('search', ''));
        $role = $request->input('role');
        $status = (string) $request->input('status', 'active'); // active|trashed|all

        $query = User::query()->with(['scenarios', 'campaigns', 'pages', 'sections']);

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
     * @param User|null $user
     * @return \Inertia\Response
     */
    public function show(User $user = null)
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
     * @return \Illuminate\Http\JsonResponse
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

        $request->session()->put('auth.password_confirmed_at', time());

        return response()->json(['confirmed' => true]);
    }

    /**
     * Affiche le formulaire de création d'utilisateur.
     *
     * @return \Inertia\Response
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
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $data = $request->validated();

        if (($data['role'] ?? User::ROLE_USER) === User::ROLE_SUPER_ADMIN) {
            return back()->withErrors(['role' => 'Impossible de créer directement un super administrateur.']);
        }

        if (!isset($data['role']) || !array_key_exists((int) $data['role'], User::ROLES)) {
            $data['role'] = User::ROLE_USER;
        }

        if ($request->hasFile('avatar')) {
            unset($data['avatar']);
        }
        $data['notifications_enabled'] = $data['notifications_enabled'] ?? true;
        $data['notification_channels'] = $data['notification_channels'] ?? ['database'];
        $user = User::create($data);
        if ($request->hasFile('avatar')) {
            $ext = $request->file('avatar')->getClientOriginalExtension() ?: 'png';
            $customName = $user->getMediaFileNameForCollection('avatars', $ext);
            $adder = $user->addMediaFromRequest('avatar');
            if ($customName !== null && $customName !== '') {
                $adder->usingFileName($customName);
            }
            $media = $adder->toMediaCollection('avatars');
            $user->update(['avatar' => $media->getUrl()]);
        }
        return redirect()->route('user.admin.edit', $user)->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'utilisateur.
     * Si aucun utilisateur n'est spécifié, affiche le formulaire pour l'utilisateur connecté.
     *
     * @param User|null $user
     * @return \Inertia\Response
     */
    public function edit(User $user = null)
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
     * @return \Inertia\Response
     */
    public function settings()
    {
        $user = Auth::user();
        $this->authorize('update', $user);
        $user->load([]);
        return Inertia::render('Pages/user/Settings', [
            'user' => new UserResource($user),
            'notificationTypes' => config('notifications.types', []),
            'notificationChannelsLabels' => config('notifications.channels', []),
            'notificationFrequencies' => config('notifications.frequencies', []),
        ]);
    }

    /**
     * Met à jour un utilisateur (profil courant ou admin).
     *
     * @param UpdateUserRequest $request
     * @param User|null $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);
        $old = clone $user;
        $data = $request->validated();
        // Gestion de l'avatar (Media Library)
        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatars');
            $ext = $request->file('avatar')->getClientOriginalExtension() ?: 'png';
            $customName = $user->getMediaFileNameForCollection('avatars', $ext);
            $adder = $user->addMediaFromRequest('avatar');
            if ($customName !== null && $customName !== '') {
                $adder->usingFileName($customName);
            }
            $media = $adder->toMediaCollection('avatars');
            $data['avatar'] = $media->getUrl();
        }
        // Normalisation des préférences de notifications : forme { channels: [], frequency: 'instant'|... }
        if (array_key_exists('notification_preferences', $data)) {
            $allowedTypes = array_keys(config('notifications.types', []));
            $prefs = $data['notification_preferences'];
            if (is_array($prefs)) {
                $data['notification_preferences'] = [];
                foreach (array_intersect_key($prefs, array_flip($allowedTypes)) as $type => $val) {
                    $channels = [];
                    $frequency = config('notifications.types.' . $type . '.frequency_default', 'instant');
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
        NotificationService::notifyProfileModified($user, Auth::user(), $old);
        // Redirection selon le contexte ou la demande (ex. depuis la page paramètres)
        if ($request->input('redirect') === 'settings' && $user->id === Auth::id()) {
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
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(User $user)
    {
        $this->authorize('delete', $user);
        try {
            NotificationService::notifyUserDeleted($user, Auth::user());
        } catch (\Throwable $e) {
            report($e);
        }
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Utilisateur supprimé.');
    }

    /**
     * Supprime définitivement un utilisateur (admin only).
     * Supprime aussi l'avatar physique si présent.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(User $user)
    {
        $this->authorize('forceDelete', $user);
        $user->clearMediaCollection('avatars');
        $user->forceDelete();
        return redirect()->route('user.index')->with('success', 'Utilisateur supprimé définitivement.');
    }

    /**
     * Restaure un utilisateur supprimé.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(int $user)
    {
        $model = User::withTrashed()->findOrFail($user);
        $this->authorize('restore', $model);
        $model->restore();
        return redirect()->route('user.index')->with('success', 'Utilisateur restauré.');
    }

    /**
     * Met à jour uniquement l'avatar de l'utilisateur (endpoint dédié, UX moderne).
     * Si aucun utilisateur n'est spécifié, utilise l'utilisateur connecté.
     *
     * @param Request $request
     * @param User|null $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(Request $request, User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);
        
        // Vérifier que le fichier est présent
        if (!$request->hasFile('avatar')) {
            return redirect()->back()->withErrors(['avatar' => 'Aucun fichier n\'a été téléchargé.']);
        }
        
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp,svg', 'max:5120'], // 5MB max
        ]);

        $user->clearMediaCollection('avatars');
        $ext = $request->file('avatar')->getClientOriginalExtension() ?: 'png';
        $customName = $user->getMediaFileNameForCollection('avatars', $ext);
        $adder = $user->addMediaFromRequest('avatar');
        if ($customName !== null && $customName !== '') {
            $adder->usingFileName($customName);
        }
        $media = $adder->toMediaCollection('avatars');
        $user->update(['avatar' => $media->getUrl()]);
        
        // Recharger l'utilisateur avec les relations pour retourner les données complètes
        $user->refresh();
        
        return redirect()->back()->with('success', 'Avatar mis à jour.');
    }

    /**
     * Supprime uniquement l'avatar de l'utilisateur (endpoint dédié, UX moderne).
     * Si aucun utilisateur n'est spécifié, utilise l'utilisateur connecté.
     *
     * @param User|null $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAvatar(User $user = null)
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRole(\Illuminate\Http\Request $request, \App\Models\User $user)
    {
        $this->authorize('updateRole', $user);

        $request->validate([
            'role' => ['required', \Illuminate\Validation\Rule::in(array_keys(\App\Models\User::ROLES))],
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
        if ($roleValue === User::ROLE_ADMIN && $request->user()->role !== User::ROLE_SUPER_ADMIN) { // admin = 4, super_admin = 5
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
     * @param \Illuminate\Http\Request $request
     * @param User|null $user Utilisateur dont on modifie le mot de passe (null = utilisateur connecté)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(\Illuminate\Http\Request $request, User $user = null)
    {
        $user = $user ?? Auth::user();
        $isSelfUpdate = $user->id === Auth::id();

        if ($isSelfUpdate) {
            $this->authorize('update', $user);
        } else {
            $this->authorize('resetPassword', $user);
        }

        $rules = [
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        // En mise à jour de son propre mot de passe, le mot de passe courant est toujours requis.
        if ($isSelfUpdate) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'password' => \Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe mis à jour.');
    }
}
