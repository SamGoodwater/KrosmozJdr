<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Rules\FileRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function listing(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', User::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $users = User::with(['scenarios', 'campaigns'])->paginate($paginationMaxDisplay);

        return Inertia::render('Organisms/User/Dashboard.vue', [
            'users' => UserResource::collection($users),
        ]);
    }

    public function dashboard(Request $request): \Inertia\Response
    {
        $user = Auth::user();
        $this->authorize('view', $user);

        $user->load(['scenarios', 'campaigns']);

        return Inertia::render('Organisms/User/Dashboard', [
            'user' => new UserResource($user),
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

    public function edit(User $user = null): \Inertia\Response
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);

        $user->load(['scenarios', 'campaigns']);

        return Inertia::render('Organisms/User/Edit', [
            'user' => new UserResource($user),
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'isAdminEdit' => Auth::user()->id !== $user->id,
        ]);
    }

    public function update(UserFilterRequest $request, User $user = null): JsonResponse|RedirectResponse
    {
        try {
            $user = $user ?? Auth::user();
            $this->authorize('update', $user);
            $old_user = clone $user;

            if ($request->email && $request->email !== $user->email) {
                $user->email_verified_at = null;
            }

            $data = $request->only(['name', 'email', 'role', 'avatar']);
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update(array_filter($data));
            $user->scenarios()->sync($request->input('scenarios', []));
            $user->campaigns()->sync($request->input('campaigns', []));

            event(new NotificationSuperAdminEvent('user', "update", $user, $old_user));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mise à jour réussie',
                    'data' => new UserResource($user)
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour',
                    'errors' => [$e->getMessage()]
                ], 422);
            }

            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour']);
        }
    }

    public function updateAvatar(Request $request, User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);

        try {
            $request->validate([
                'file' => FileRules::rules(
                    FileRules::TYPE_IMAGE,
                    5120,    // 5MB
                    true,    // Required
                    false    // Non nullable
                )['file']
            ]);

            if ($request->hasFile('file')) {
                // Supprimer l'ancien avatar s'il existe
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Stocker le nouveau fichier
                $file = $request->file('file');
                $extension = strtolower($file->getClientOriginalExtension());
                $fileName = $user->uniqid . '_' . time();

                if (in_array($extension, ['svg', 'eps', 'pdf'])) {
                    // Pour les formats vectoriels, stocker directement
                    $path = $file->storeAs('users/avatars', $fileName . '.' . $extension, 'public');
                } else {
                    // Pour les images bitmap, convertir en WebP
                    $path = 'users/avatars/' . $fileName . '.webp';
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($file->getRealPath());
                    $image->scaleDown(width: 800, height: 800);
                    Storage::disk('public')->put($path, (string) $image->toWebp(70));
                }

                // Mettre à jour l'utilisateur
                $user->avatar = $path;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Avatar mis à jour avec succès',
                    'data' => new UserResource($user)
                ]);
            }

            throw new \Exception('Erreur lors du traitement du fichier');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function deleteAvatar(User $user = null)
    {
        $user = $user ?? Auth::user();
        $this->authorize('update', $user);

        try {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                DataService::deleteFile($user, 'avatar');
                $user->avatar = null;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Avatar supprimé avec succès',
                    'data' => new UserResource($user)
                ]);
            }

            throw new \Exception('Aucun avatar à supprimer');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
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
}
