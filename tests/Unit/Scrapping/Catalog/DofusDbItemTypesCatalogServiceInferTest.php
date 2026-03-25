<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Catalog;

use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\Http\DofusDbClient;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService::inferSuperTypeIdFromItemRaw
 */
class DofusDbItemTypesCatalogServiceInferTest extends TestCase
{
    public function test_infer_from_type_super_type_id(): void
    {
        $svc = new DofusDbItemTypesCatalogService($this->createStub(DofusDbClient::class));
        $id = $svc->inferSuperTypeIdFromItemRaw([
            'type' => ['id' => 6, 'superTypeId' => 2],
        ]);
        $this->assertSame(2, $id);
    }

    public function test_infer_from_type_nested_super_type(): void
    {
        $svc = new DofusDbItemTypesCatalogService($this->createStub(DofusDbClient::class));
        $id = $svc->inferSuperTypeIdFromItemRaw([
            'type' => ['id' => 1, 'superType' => ['id' => 1, 'name' => ['fr' => 'Amulette']]],
        ]);
        $this->assertSame(1, $id);
    }

    public function test_infer_from_top_level_super_type_id(): void
    {
        $svc = new DofusDbItemTypesCatalogService($this->createStub(DofusDbClient::class));
        $id = $svc->inferSuperTypeIdFromItemRaw(['superTypeId' => 9, 'typeId' => 51]);
        $this->assertSame(9, $id);
    }

    public function test_infer_returns_null_when_missing(): void
    {
        $svc = new DofusDbItemTypesCatalogService($this->createStub(DofusDbClient::class));
        $id = $svc->inferSuperTypeIdFromItemRaw(['type' => ['id' => 99]]);
        $this->assertNull($id);
    }
}
