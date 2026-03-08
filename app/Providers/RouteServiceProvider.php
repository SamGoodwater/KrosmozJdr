<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        Route::bind('user', function ($value) {
            return \App\Models\User::withTrashed()->findOrFail($value);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('privacy-actions', function (Request $request) {
            $key = ($request->user()?->id ?? 'guest') . '|' . $request->ip();
            return [
                Limit::perMinutes(15, 3)->by($key),
            ];
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Charger les routes auth
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            // Charger les routes user
            Route::middleware('web')
                ->group(base_path('routes/user.php'));

            // Charger les routes page
            Route::middleware('web')
                ->group(base_path('routes/page.php'));

            // Charger les routes file
            Route::middleware('web')
                ->group(base_path('routes/file.php'));
        });
    }
}
