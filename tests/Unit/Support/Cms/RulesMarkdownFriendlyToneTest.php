<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesMarkdownFriendlyTone;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RulesMarkdownFriendlyToneTest extends TestCase
{
    #[Test]
    public function it_converts_formal_imperatives_to_tu_form(): void
    {
        $input = 'Lancez un dé. Ajoutez le modificateur. Ne demandez pas de jet trivial.';

        $output = RulesMarkdownFriendlyTone::apply($input);

        $this->assertSame(
            'Lance un dé. Ajoute le modificateur. Ne demande pas de jet trivial.',
            $output
        );
    }

    #[Test]
    public function it_rewrites_description_infinitive_lines(): void
    {
        $input = '**Description** : Expliquer la mécanique d20, les classes de difficulté.';

        $output = RulesMarkdownFriendlyTone::apply($input);

        $this->assertStringContainsString('expliqué clairement', $output);
        $this->assertStringNotContainsString('Expliquer la', $output);
    }

    #[Test]
    public function it_softens_stiff_opening_phrases(): void
    {
        $input = 'KrosmozJDR utilise le **dé à 20 faces (d20)** comme base pour résoudre la plupart des actions.';

        $output = RulesMarkdownFriendlyTone::apply($input);

        $this->assertStringContainsString('tu lances un **d20**', $output);
    }
}
