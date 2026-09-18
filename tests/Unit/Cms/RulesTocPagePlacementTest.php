<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use App\Models\User;
use App\Support\Cms\RulesTocPagePlacement;
use Tests\TestCase;

class RulesTocPagePlacementTest extends TestCase
{
    public function test_chapter_five_is_reserved_for_game_masters(): void
    {
        foreach (['5', '5.1', '5.2.3'] as $number) {
            $placement = RulesTocPagePlacement::forNumber($number);
            $this->assertSame('Pour les MJ', $placement['menu_group'], $number);
            $this->assertSame(User::ROLE_GAME_MASTER, $placement['read_level'], $number);
        }
    }

    public function test_other_chapters_stay_in_player_rules(): void
    {
        foreach (['1', '1.1.4', '4.4.5', '6.1.1'] as $number) {
            $placement = RulesTocPagePlacement::forNumber($number);
            $this->assertSame('Règles', $placement['menu_group'], $number);
            $this->assertSame(User::ROLE_GUEST, $placement['read_level'], $number);
        }
    }
}
