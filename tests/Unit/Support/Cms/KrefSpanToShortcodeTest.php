<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\KrefShortcodeReplacer;
use App\Support\Cms\KrefSpanToShortcode;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class KrefSpanToShortcodeTest extends TestCase
{
    #[Test]
    public function it_round_trips_characteristic_shortcode(): void
    {
        $html = (new KrefShortcodeReplacer)->replace(
            'Coût en [[kref:characteristic:action_points_creature|PA]].'
        );

        $this->assertSame(
            'Coût en [[kref:characteristic:action_points_creature|PA]].',
            KrefSpanToShortcode::apply($html)
        );
    }

    #[Test]
    public function it_round_trips_page_section_at_separator(): void
    {
        $html = (new KrefShortcodeReplacer)->replace(
            'Voir [[kref:pageSection:regles-3-2-combat@regle-3-2-2-tour-de-jeu-et-actions|Tour de jeu]].'
        );

        $this->assertSame(
            'Voir [[kref:pageSection:regles-3-2-combat@regle-3-2-2-tour-de-jeu-et-actions|Tour de jeu]].',
            KrefSpanToShortcode::apply($html)
        );
    }

    #[Test]
    public function it_reads_legacy_data_attributes(): void
    {
        $html = '<span class="kref" data-kref-type="page" data-kref-payload="{&quot;pageSlug&quot;:&quot;regles-2-2-les-caracteristiques&quot;}">Caractéristiques</span>';

        $this->assertSame(
            '[[kref:page:regles-2-2-les-caracteristiques|Caractéristiques]]',
            KrefSpanToShortcode::apply($html)
        );
    }
}
