<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Request;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function listing(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', User::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $users = User::paginate($paginationMaxDisplay);

        return Inertia::render('Organisms/User/Dashboard.vue', [
            'users' => $users,
        ]);
    }

    public function dashboard(Request $request): \Inertia\Response
    {
        $user = Auth::user();
        $this->authorize('view', $user);

        return Inertia::render('Organisms/User/Dashboard', [
            'verifiedEmail' => $user->hasVerifiedEmail(),
            'user' => $user,
            'resources' => $user->resources,
            'panoplies' => $user->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Organisms/Users/Create');
    }

    public function store(UserFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = DataService::extractData($request, new User, [
            [
                'disk' => 'modules',
                'path_name' => 'users',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $user = User::create($data);
        $user->scenarios()->attach($request->input('scenarios'));
        $user->campaigns()->attach($request->input('campaigns'));

        event(new NotificationSuperAdminEvent('user', 'create',  $user));

        return redirect()->route('user.dashboard', ['user' => $user]);
    }

    public function edit(): \Inertia\Response
    {
        $user = Auth::user();
        $this->authorize('update', $user);

        return Inertia::render('Organisms/User/Edit', [
            'user' => $user,
            'resources' => $user->resources,
            'panoply' => $user->panoply,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function update(UserFilterRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $this->authorize('update', $user);
        $old_user = clone $user;

        if ($request->email !== $user->email) {
            $user->email_verified_at = null;
        }

        $data = DataService::extractData($request, $user, [
            [
                'disk' => 'modules',
                'path_name' => 'users',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);

        if ($data === []) {
            return redirect()->back()->withInput();
        }

        $user->update($data);

        event(new NotificationSuperAdminEvent('user', "update", $user, $old_user));

        return redirect()->route('user.dashboard');
    }

    public function delete(UserFilterRequest $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);
        event(new NotificationSuperAdminEvent('user', "delete", $user));

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.index');
    }

    public function forceDelete(UserFilterRequest $request, User $user): RedirectResponse
    {
        $this->authorize('forceDelete', $user);

        $user->scenarios()->detach();
        $user->campaigns()->detach();

        DataService::deleteFile($user, 'image');
        event(new NotificationSuperAdminEvent('user', "forced_delete", $user));

        Auth::logout();
        $user->forceDelete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.index');
    }

    public function restore(User $user): RedirectResponse
    {
        $this->authorize('restore', $user);

        $user->restore();

        return redirect()->route('user.index');
    }

    public function adminEdit(User $user): \Inertia\Response
    {
        $this->authorize('update', $user);

        return Inertia::render('Organisms/User/Edit', [
            'user' => $user,
            'resources' => $user->resources,
            'panoply' => $user->panoply,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'isAdminEdit' => true,
        ]);
    }

    public function adminUpdate(User $user, UserFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $user);
        $old_user = clone $user;

        if ($user->email !== $request->email) {
            $user->email_verified_at = null;
        }

        $data = DataService::extractData($request, $user, [
            [
                'disk' => 'modules',
                'path_name' => 'users',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);

        if ($data === []) {
            return redirect()->back()->withInput();
        }

        $user->update($data);
        $user->scenarios()->sync($request->input('scenarios'));
        $user->campaigns()->sync($request->input('campaigns'));

        event(new NotificationSuperAdminEvent('user', "update", $user, $old_user));

        return redirect()->route('user.dashboard', ['user' => $user]);
    }
}
