<?php

namespace App\Providers;

use App\Models\Characteristic;
use App\Models\CharacteristicEntity;
use App\Models\DofusdbConversionFormula;
use App\Models\EquipmentSlot;
use App\Models\EquipmentSlotCharacteristic;
use App\Observers\CharacteristicConfigObserver;
use App\Observers\DofusdbConversionFormulaObserver;
use App\Observers\EquipmentCharacteristicConfigObserver;
use App\Services\Characteristic\CharacteristicService;
use App\Services\Characteristic\EquipmentCharacteristicService;
use App\Services\Scrapping\ConversionHandlerRegistry;
use App\Services\Scrapping\DofusDbConversionFormulaService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CharacteristicService::class);
        $this->app->singleton(EquipmentCharacteristicService::class);
        $this->app->singleton(DofusDbConversionFormulaService::class);
        $this->app->singleton(ConversionHandlerRegistry::class);
        $this->app->singleton(ConfigLoader::class, static fn () => ConfigLoader::default());
        $this->app->singleton(CollectService::class, static fn () => new CollectService(
            app(ConfigLoader::class),
            app(DofusDbClient::class)
        ));
        $this->app->singleton(Orchestrator::class, static fn () => Orchestrator::default());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Model::unguard();

        Characteristic::observe(CharacteristicConfigObserver::class);
        CharacteristicEntity::observe(CharacteristicConfigObserver::class);
        EquipmentSlot::observe(EquipmentCharacteristicConfigObserver::class);
        EquipmentSlotCharacteristic::observe(EquipmentCharacteristicConfigObserver::class);
        DofusdbConversionFormula::observe(DofusdbConversionFormulaObserver::class);
    }
}
