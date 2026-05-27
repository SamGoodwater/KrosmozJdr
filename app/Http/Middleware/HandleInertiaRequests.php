<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserLightResource;
use App\Models\DataSubjectRequest;
use App\Support\EntityPermissions\EntityPermissionService;
use App\Support\OAuthConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        private readonly EntityPermissionService $permissionService
    ) {}

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
     * Props recalculées à chaque requête Inertia (auth, flash, URL courante pour Ziggy).
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'ziggy_location' => fn () => $request->url(),
            'pending_erasure' => Inertia::defer(fn () => $this->resolvePendingErasure($request)),
            // Toujours renvoyé (même sur reload partiel `only`) pour garder le header auth à jour.
            'auth' => Inertia::always([
                'user' => function () use ($request) {
                    $user = $request->user();
                    if (! $user) {
                        return null;
                    }

                    $user->loadMissing('oauthAccounts');

                    return (new UserLightResource($user))->toArray($request);
                },
                'isLogged' => $request->user() !== null,
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
                'notifications_unread_count' => Inertia::defer(
                    fn () => $this->resolveNotificationsUnreadCount($request),
                    'sidebar'
                ),
            ]),
            'flash' => Inertia::always([
                'success' => fn () => session('success'),
                'error' => fn () => session('error'),
                'warning' => fn () => session('warning'),
                'info' => fn () => session('info'),
                'status' => fn () => session('status'),
            ]),
        ];
    }

    /**
     * Props mémorisées côté client après la première visite (routes, permissions, OAuth).
     *
     * @return array<string, callable>
     */
    public function shareOnce(Request $request): array
    {
        return [
            'permissions' => fn () => $this->permissionService->forUser($request->user()),
            'ziggy' => fn () => (new Ziggy)->toArray(),
            'oauth_enabled_providers' => fn () => OAuthConfig::enabledProviders(),
        ];
    }

    /**
     * @return array{expires_at: string}|null
     */
    private function resolvePendingErasure(Request $request): ?array
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        $dsr = DataSubjectRequest::query()
            ->where('user_id', $user->id)
            ->where('type', DataSubjectRequest::TYPE_ERASURE)
            ->where('status', DataSubjectRequest::STATUS_PENDING)
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
    }

    private function resolveNotificationsUnreadCount(Request $request): int
    {
        if (! $request->user()) {
            return 0;
        }

        return $request->user()->unreadNotifications()->whereNull('archived_at')->count();
    }
}
