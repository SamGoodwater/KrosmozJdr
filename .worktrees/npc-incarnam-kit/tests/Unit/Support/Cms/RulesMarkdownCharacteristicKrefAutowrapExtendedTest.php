<?php

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesMarkdownCharacteristicKrefAutowrap;
use PHPUnit\Framework\TestCase;

class RulesMarkdownCharacteristicKrefAutowrapExtendedTest extends TestCase
{
    public function test_wraps_points_d_action_plain(): void
    {
        $md = 'La ressource Points d\'action (PA) définit le tour.';
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertStringContainsString('[[kref:characteristic:action_points_creature|Points d\'action (PA)]]', $out);
    }

    public function test_preserves_existing_shortcode(): void
    {
        $md = 'Déjà [[kref:characteristic:action_points_creature|Points d\'action]] ici.';
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertSame(1, substr_count($out, '[[kref:characteristic:action_points_creature|Points d\'action]]'));
    }
}
