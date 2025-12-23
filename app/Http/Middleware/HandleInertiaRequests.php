<?php

namespace App\Http\Middleware;

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
            'auth' => [
                'user' => function () use ($request) {
                    if (!$request->user()) {
                        return null;
                    }
                    return (new UserLightResource($request->user()))->toArray($request);
                },
                'isLogged' => fn() => $request->user() !== null,
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
        ];
    }
}
