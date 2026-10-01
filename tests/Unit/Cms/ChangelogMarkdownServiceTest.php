<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use App\Services\ChangelogMarkdownService;
use Tests\TestCase;

/**
 * @description Découpe le markdown de la frise en étapes déjà là / prochaine / ensuite.
 */
class ChangelogMarkdownServiceTest extends TestCase
{
    public function test_parse_roadmap_assigns_states_from_the_label(): void
    {
        $steps = (new ChangelogMarkdownService)->parseRoadmap(<<<'MD'
# Où on va

## 1.3 · Déjà là

Le jeu se joue.

## 1.4 · Prochaine version

Les campagnes, les scénarios, et les fiches.

## 1.5 · Ensuite

Des outils pour les combats.
MD);

        $this->assertCount(3, $steps);
        $this->assertSame(['1.3', 'done', 'Le jeu se joue.'], [$steps[0]['version'], $steps[0]['state'], $steps[0]['text']]);
        $this->assertSame('next', $steps[1]['state']);
        $this->assertSame('later', $steps[2]['state']);
        $this->assertStringContainsString('fiches', $steps[1]['text']);
    }
}
