<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\KrefShortcodeReplacer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KrefShortcodeReplacerTest extends TestCase
{
    #[Test]
    public function it_converts_characteristic_shortcode_to_kref_span(): void
    {
        $html = (new KrefShortcodeReplacer)->replace(
            'Coût en [[kref:characteristic:action_points_creature|PA]].'
        );

        $this->assertStringContainsString('<span class="kref"', $html);
        $this->assertStringContainsString('>PA</span>', $html);
        $this->assertStringNotContainsString('[[kref:', $html);
    }

    #[Test]
    public function it_converts_page_section_shortcode_with_at_separator(): void
    {
        $html = (new KrefShortcodeReplacer)->replace(
            'Voir [[kref:pageSection:regles-3-2-combat@regle-3-2-2-tour-de-jeu-et-actions|Tour de jeu]].'
        );

        $this->assertStringContainsString('class="kref kref--nav"', $html);
        $this->assertStringContainsString('>Tour de jeu</span>', $html);
    }

    #[Test]
    public function it_leaves_unknown_shortcodes_unchanged(): void
    {
        $input = '[[kref:unknown:foo|Bar]]';

        $this->assertSame($input, (new KrefShortcodeReplacer)->replace($input));
    }
}
