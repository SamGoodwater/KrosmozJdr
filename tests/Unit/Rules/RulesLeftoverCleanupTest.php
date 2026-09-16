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
        $this->assertStringContainsString('annexe 6.1', $text);
        $this->assertStringNotContainsString('1. Choisir une classe', $text);
    }

    public function test_changelog_lives_in_annex_six(): void
    {
        $rules = dirname(__DIR__, 3).'/private/game/rules';
        $this->assertFileExists($rules.'/6-Annexes/6.1-changelog-et-historique/6.1.1-chronologie-des-versions.md');
        $this->assertDirectoryDoesNotExist($rules.'/1-Introduction/1.3-changelog-et-historique');

        $toc = (string) file_get_contents($rules.'/TABLE_DES_MATIERES.md');
        $this->assertStringContainsString('## 6. Annexes', $toc);
        $this->assertStringNotContainsString('### 1.3 Changelog', $toc);
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
}
