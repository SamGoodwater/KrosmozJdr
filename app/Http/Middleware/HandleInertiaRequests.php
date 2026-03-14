<?php

namespace App\Http\Middleware;

use App\Support\OAuthConfig;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Http\Resources\UserLightResource;
use App\Support\EntityPermissions\EntityPermissionService;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $permissionService = new EntityPermissionService();
        return [
            ...parent::share($request),
            'pending_erasure' => function () use ($request) {
                $user = $request->user();
                if (! $user) {
                    return null;
                }
                $dsr = \App\Models\DataSubjectRequest::query()
                    ->where('user_id', $user->id)
                    ->where('type', \App\Models\DataSubjectRequest::TYPE_ERASURE)
                    ->where('status', \App\Models\DataSubjectRequest::STATUS_PENDING)
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '>', now())
                    ->latest('id')
                    ->first();
                if (! $dsr) {
                    return null;
                }
                return [
                    'expires_at' => $dsr->expires_at->toIso8601String(),
                ];
            },
            'auth' => [
                'user' => function () use ($request) {
                    if (!$request->user()) {
                        return null;
                    }
                    return (new UserLightResource($request->user()))->toArray($request);
                },
                'isLogged' => fn () => $request->user() !== null,
                'password_recently_confirmed' => function () use ($request) {
                    if (! $request->user()) {
                        return false;
                    }
                    $session = $request->session();
                    if (! $session->has('auth.password_confirmed_at')) {
                        return false;
                    }
                    $lastActivity = $session->get(
                        'auth.password_last_activity_at',
                        $session->get('auth.password_confirmed_at', 0)
                    );
                    $timeout = (int) config('auth.password_inactivity_timeout', 3600);

                    return (time() - $lastActivity) <= $timeout;
                },
                'notifications_unread_count' => function () use ($request) {
                    if (! $request->user()) {
                        return 0;
                    }
                    return $request->user()->unreadNotifications()->whereNull('archived_at')->count();
                },
            ],
            'flash' => [
                'success' => fn () => session('success'),
                'error' => fn () => session('error'),
                'warning' => fn () => session('warning'),
                'info' => fn () => session('info'),
                'status' => fn () => session('status'),
            ],
            /**
             * Permissions globales (par entité) exposées au frontend.
             *
             * Structure:
             * permissions: {
             *   entities: {
             *     resources: { viewAny, create, createAny, updateAny, deleteAny },
             *     items: { ... },
             *   }
             * }
             */
            'permissions' => fn() => $permissionService->forUser($request->user()),
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            /** Providers OAuth activés (credentials configurés dans .env). */
            'oauth_enabled_providers' => fn () => OAuthConfig::enabledProviders(),
        ];
    }
}
