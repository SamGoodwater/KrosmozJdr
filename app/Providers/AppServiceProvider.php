<?php

namespace App\Providers;

use App\Services\Characteristic\Conversion\ConversionFunctionRegistry;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\CollectAliasResolver;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Config\ScrappingMappingService;
use App\Services\Scrapping\Core\Conversion\SpellEffects\DofusdbEffectMappingService;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use App\Models\DofusdbEffectMapping;
use App\Services\Media\EnsureDirectoryMediaFilesystem;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Filesystem as MediaLibraryFilesystem;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CharacteristicGetterService::class);
        $this->app->singleton(CharacteristicLimitService::class);
        $this->app->singleton(CharacteristicFormulaService::class);
        $this->app->singleton(ConversionFunctionRegistry::class);
        $this->app->singleton(DofusConversionService::class);

        $this->app->singleton(ScrappingMappingService::class);
        $this->app->singleton(ConfigLoader::class, static function () {
            return new ConfigLoader(
                base_path('resources/scrapping/config'),
                app(ScrappingMappingService::class)
            );
        });
        $this->app->singleton(CollectAliasResolver::class, static fn () => CollectAliasResolver::default());
        $this->app->singleton(CollectService::class, static fn () => new CollectService(
            app(ConfigLoader::class),
            app(DofusDbClient::class),
            app(CollectAliasResolver::class)
        ));
        $this->app->singleton(DofusdbEffectMappingService::class, static function ($app) {
            return new DofusdbEffectMappingService($app->make(DofusdbEffectMapping::class));
        });
        $this->app->singleton(Orchestrator::class, static fn () => Orchestrator::default());

        // Filesystem Media Library : crée les dossiers avant écriture (scrapping, etc.)
        $this->app->singleton(MediaLibraryFilesystem::class, EnsureDirectoryMediaFilesystem::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Model::unguard();

        // Map court pour effect_usages : le scrapping stocke 'spell'/'item'/… au lieu du FQCN.
        // morphMap (sans enforce) : ajoute les mappings sans imposer aux autres relations polymorphes (User, etc.)
        Relation::morphMap([
            'spell' => \App\Models\Entity\Spell::class,
            'item' => \App\Models\Entity\Item::class,
            'consumable' => \App\Models\Entity\Consumable::class,
            'resource' => \App\Models\Entity\Resource::class,
        ]);

        $this->configureRateLimiting();
        $this->registerConversionFunctions();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('privacy-actions', function (\Illuminate\Http\Request $request) {
            $key = ($request->user()?->id ?? 'guest') . '|' . $request->ip();

            return Limit::perMinutes(15, 10)->by($key);
        });
    }

    private function registerConversionFunctions(): void
    {
        $registry = $this->app->make(ConversionFunctionRegistry::class);
        $registry->register('identity', static function (float $value): float {
            return $value;
        }, 'Identité (inchangé)');
        $registry->register('convertToDice', static function (float $value): float {
            return $value;
        }, 'Notation dés (ndX / ndX+y)');
    }
}
