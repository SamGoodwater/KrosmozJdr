<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;

/**
 * Garde-fous leftover règles : INDEX de jeu, annexe changelog, +4 sur les fiches playable.
 */
final class RulesLeftoverCleanupTest extends TestCase
{
    public function test_index_is_a_game_index_not_a_title_dump(): void
    {
        $path = dirname(__DIR__, 3).'/private/game/rules/INDEX.md';
        $text = (string) file_get_contents($path);
        $lines = preg_split("/\r\n|\n|\r/", $text) ?: [];

        $this->assertLessThan(200, count($lines), 'INDEX.md doit rester un index de jeu, pas un dump de titres');
        $this->assertStringContainsString('Index de **jeu**', $text);
        $this->assertStringContainsString('+4', $text);
        $this->assertStringContainsString('Changelog', $text);
        $this->assertStringNotContainsString('annexe 6.1', $text);
        $this->assertStringNotContainsString('1. Choisir une classe', $text);
    }

    public function test_changelog_is_not_in_the_rules_book(): void
    {
        $rules = dirname(__DIR__, 3).'/private/game/rules';
        $this->assertDirectoryDoesNotExist($rules.'/6-Annexes');
        $this->assertDirectoryDoesNotExist($rules.'/1-Introduction/1.3-changelog-et-historique');

        $toc = (string) file_get_contents($rules.'/TABLE_DES_MATIERES.md');
        $this->assertStringNotContainsString('## 6. Annexes', $toc);
        $this->assertStringNotContainsString('### 1.3 Changelog', $toc);
        $this->assertStringContainsString('## 5. Ressources et équilibrage', $toc);
    }

    public function test_playable_item_main_stat_bonuses_stay_at_plus_four(): void
    {
        $root = dirname(__DIR__, 3).'/database/seeders/data/entities/items';
        $keys = ['vitality', 'strength', 'intelligence', 'chance', 'agility', 'wisdom'];
        $files = glob($root.'/*.json') ?: [];
        $this->assertNotEmpty($files);

        foreach ($files as $path) {
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $bonus = $data['item']['bonus'] ?? $data['bonus'] ?? [];
            if (! is_array($bonus)) {
                continue;
            }
            foreach ($keys as $key) {
                if (! isset($bonus[$key]) || ! is_numeric($bonus[$key])) {
                    continue;
                }
                $this->assertLessThanOrEqual(
                    4,
                    abs((int) $bonus[$key]),
                    basename($path).' '.$key
                );
            }
        }
    }

    public function test_resources_docs_do_not_teach_plus_eight_main_stats(): void
    {
        $root = dirname(__DIR__, 3).'/private/game/resources';
        $files = [
            $root.'/PROPOSITIONS_FORMULES_ET_PROPRIETES.md',
            $root.'/INVENTAIRE_MODIFS_CARACTERISTIQUES_PRINCIPALES.md',
            $root.'/AUDIT_COHERENCE_GLOBALE.md',
        ];

        foreach ($files as $path) {
            $text = (string) file_get_contents($path);
            $this->assertStringNotContainsString(
                'max 8 (6 équip.',
                $text,
                basename($path)
            );
            $this->assertStringNotContainsString(
                'Le seeder utilise **max 8**',
                $text,
                basename($path)
            );
        }
    }
}
