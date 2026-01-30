<?php

namespace Tests\Feature\Scrapping;

use App\Services\Scrapping\DataCollect\DataCollectService;
use App\Services\Scrapping\Http\DofusDbClient;
use Tests\TestCase;

/**
 * Tests unitaires (via réflexion) pour la détection typeId -> "ressource" dans DataCollectService.
 *
 * On veut :
 * - allowlist = source de vérité (par défaut, on étend progressivement)
 * - denylist prioritaire (blacklist)
 */
class ResourceTypeIdDetectionTest extends TestCase
{
    public function test_allowlist_allows_known_resource_type_ids(): void
    {
        // Forcer le mode "config allow/deny lists" (sinon la source de vérité est la DB)
        config()->set('scrapping.data_collect.resources.use_database_registry', false);
        config()->set('scrapping.data_collect.resources.type_ids_allowlist', [15, 35]);
        config()->set('scrapping.data_collect.resources.type_ids_denylist', []);

        $service = new DataCollectService(
            app(DofusDbClient::class),
            app(\App\Services\Scrapping\DataCollect\ConfigDrivenDofusDbCollector::class),
        );

        $method = new \ReflectionMethod($service, 'isResourceType');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($service, 15));
        $this->assertTrue($method->invoke($service, 35));
        $this->assertFalse($method->invoke($service, 999)); // inconnu => false par défaut
    }

    public function test_denylist_is_prioritary_over_allowlist(): void
    {
        // Forcer le mode "config allow/deny lists" (sinon la source de vérité est la DB)
        config()->set('scrapping.data_collect.resources.use_database_registry', false);
        config()->set('scrapping.data_collect.resources.type_ids_allowlist', [15, 35]);
        config()->set('scrapping.data_collect.resources.type_ids_denylist', [15]);

        $service = new DataCollectService(
            app(DofusDbClient::class),
            app(\App\Services\Scrapping\DataCollect\ConfigDrivenDofusDbCollector::class),
        );

        $method = new \ReflectionMethod($service, 'isResourceType');
        $method->setAccessible(true);

        // 15 est dans la denylist => doit être false
        $this->assertFalse($method->invoke($service, 15));
        // 35 reste autorisé
        $this->assertTrue($method->invoke($service, 35));
    }
}


