<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $users = User::with(['scenarios', 'campaigns', 'pages', 'sections'])->paginate(20);
        return Inertia::render('Pages/user/Index', [
            'users' => UserResource::collection($users),
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
        // Gestion de l'avatar
        if (isset($data['avatar'])) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        // Valeurs par défaut pour les notifications
        $data['notifications_enabled'] = $data['notifications_enabled'] ?? true;
        $data['notification_channels'] = $data['notification_channels'] ?? ['database'];
        $user = User::create($data);
        return redirect()->route('users.show', $user)->with('success', 'Utilisateur créé avec succès.');
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
        // Gestion de l'avatar
        if (isset($data['avatar'])) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        $user->update($data);
        NotificationService::notifyProfileModified($user, Auth::user(), $old);
        // Redirection selon le contexte
        if ($user->id === Auth::id()) {
            return redirect()->route('user.show', $user)->with('success', 'Profil mis à jour.');
        }
        return redirect()->route('user.show', $user)->with('success', 'Utilisateur mis à jour.');
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
        // Supprimer l'avatar physique si présent
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->forceDelete();
        return redirect()->route('user.index')->with('success', 'Utilisateur supprimé définitivement.');
    }

    /**
     * Restaure un utilisateur supprimé.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(User $user)
    {
        $this->authorize('restore', $user);
        $user->restore();
        return redirect()->route('user.index')->with('success', 'Utilisateur restauré.');
    }

    /**
     * Met à jour uniquement l'avatar de l'utilisateur (endpoint dédié, UX moderne).
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'], // 5MB max
        ]);
        // Supprimer l'ancien avatar si présent
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->avatar = $request->file('avatar')->store('avatars', 'public');
        $user->save();
        return redirect()->back()->with('success', 'Avatar mis à jour.');
    }

    /**
     * Supprime uniquement l'avatar de l'utilisateur (endpoint dédié, UX moderne).
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAvatar(User $user)
    {
        $this->authorize('update', $user);
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->avatar = null;
        $user->save();
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
        $this->authorize('update', $user);

        $isSelfUpdate = $user->id === Auth::id();
        $isAdmin = Auth::user()->verifyRole(User::ROLE_ADMIN);

        // Si l'utilisateur modifie son propre mot de passe, current_password est requis
        // Si un admin modifie le mot de passe d'un autre utilisateur, current_password n'est pas requis
        $rules = [
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        if ($isSelfUpdate && !$isAdmin) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'password' => \Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe mis à jour.');
    }
}
