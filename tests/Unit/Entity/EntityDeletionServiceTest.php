<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Models\User;
use App\Services\AdminActivityLogger;
use App\Services\Entity\EntityDeletionService;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

/**
 * Structure du récapitulatif d’impact (sans BDD).
 *
 * @example php artisan test --filter=EntityDeletionServiceTest
 */
class EntityDeletionServiceTest extends TestCase
{
    public function test_impact_summary_returns_expected_shape_for_plain_model(): void
    {
        $service = new EntityDeletionService(new class extends AdminActivityLogger
        {
            public function logEntity(Model $subject, string $action, ?User $actor, array $properties = []): void {}
        });

        $entity = new class extends Model {};

        $summary = $service->impactSummary($entity);

        $this->assertSame([], $summary['relations']);
        $this->assertSame(0, $summary['media_count']);
    }
}
