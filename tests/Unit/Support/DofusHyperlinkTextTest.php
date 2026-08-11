<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\DofusHyperlinkText;
use PHPUnit\Framework\TestCase;

final class DofusHyperlinkTextTest extends TestCase
{
    public function test_extracts_label_from_spell_hyperlink(): void
    {
        $this->assertSame(
            'Évadé',
            DofusHyperlinkText::toDisplayLabel('{{spell,32891,1::Évadé}}')
        );
    }

    public function test_leaves_plain_text_unchanged(): void
    {
        $this->assertSame('Pesanteur', DofusHyperlinkText::toDisplayLabel('Pesanteur'));
        $this->assertSame('', DofusHyperlinkText::toDisplayLabel(null));
        $this->assertSame('', DofusHyperlinkText::toDisplayLabel('  '));
    }

    public function test_replaces_hyperlink_inside_sentence(): void
    {
        $this->assertSame(
            "S'applique l'état Évadé.",
            DofusHyperlinkText::toDisplayLabel("S'applique l'état {{spell,32891,1::Évadé}}.")
        );
    }

    public function test_handles_multiple_hyperlinks(): void
    {
        $this->assertSame(
            'A et B',
            DofusHyperlinkText::toDisplayLabel('{{spell,1,1::A}} et {{spell,2,1::B}}')
        );
    }
}
