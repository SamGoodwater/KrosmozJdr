<?php

namespace App\Providers;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CharacteristicGetterService::class);
        $this->app->singleton(CharacteristicLimitService::class);
        $this->app->singleton(CharacteristicFormulaService::class);
        $this->app->singleton(DofusConversionService::class);

        $this->app->singleton(ConfigLoader::class, static fn () => ConfigLoader::default());
        $this->app->singleton(CollectAliasResolver::class, static fn () => CollectAliasResolver::default());
        $this->app->singleton(CollectService::class, static fn () => new CollectService(
            app(ConfigLoader::class),
            app(DofusDbClient::class)
        ));
        $this->app->singleton(Orchestrator::class, static fn () => Orchestrator::default());
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Model::unguard();
    }
}
