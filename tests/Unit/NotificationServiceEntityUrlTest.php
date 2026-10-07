<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Entity\Spell;
use App\Services\NotificationService;
use Tests\TestCase;

/**
 * URLs des liens de notification (préfixe `/entities/…`).
 *
 * @see NotificationService::entityUrl()
 * @see NotificationService::entityIndexUrl()
 */
class NotificationServiceEntityUrlTest extends TestCase
{
    public function test_entity_url_uses_entities_prefix_for_spells(): void
    {
        $spell = Spell::factory()->make();
        $spell->id = 4242;

        $path = parse_url(NotificationService::entityUrl($spell), PHP_URL_PATH);

        $this->assertSame('/entities/spells/4242', $path);
    }

    public function test_entity_index_url_points_to_spells_list(): void
    {
        $spell = Spell::factory()->make();
        $spell->id = 4242;

        $path = parse_url(NotificationService::entityIndexUrl($spell), PHP_URL_PATH);

        $this->assertSame('/entities/spells', $path);
    }
}
