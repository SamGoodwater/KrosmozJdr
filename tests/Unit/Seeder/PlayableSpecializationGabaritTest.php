<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use Tests\TestCase;

/**
 * Les 11 fiches jouables partagent le même gabarit : 5 compétences au palier 1,
 * 2 options à chaque palier, 3 aptitudes aux paliers 3 / 9 / 15.
 */
final class PlayableSpecializationGabaritTest extends TestCase
{
    private const SKILL_NAMES = [
        'Arcanes', 'Histoire', 'Investigation', 'Médecine', 'Perspicacité', 'Religion',
        'Athlétisme', 'Acrobaties', 'Discrétion', 'Escamotage', 'Nature',
        'Connaissance des créatures', 'Dressage', 'Perception', 'Survie', 'Herbaliste',
        'Persuasion', 'Représentation', 'Supercherie', 'Intimidation',
    ];

    /**
     * @return list<string>
     */
    private function sheetPaths(): array
    {
        $files = glob(database_path('seeders/data/playable-specializations/*.php'));
        $this->assertNotFalse($files);
        sort($files);

        return $files;
    }

    public function test_all_playable_sheets_share_the_same_gabarit(): void
    {
        $this->assertCount(11, $this->sheetPaths());

        foreach ($this->sheetPaths() as $path) {
            $spec = require $path;
            $name = (string) ($spec['name'] ?? basename($path));
            $this->assertTrue($spec['playable'] ?? false, $name);
            $this->assertSame([1, 3, 6, 9, 12, 15, 20], array_keys($spec['levels'] ?? []), $name);

            $skillsLine = (string) ($spec['levels'][1]['masteries']['Compétences'] ?? '');
            $found = [];
            foreach (self::SKILL_NAMES as $skill) {
                if (str_contains($skillsLine, $skill)) {
                    $found[] = $skill;
                }
            }
            $this->assertCount(5, $found, $name.' : liste de compétences = '.json_encode($found, JSON_UNESCAPED_UNICODE));

            $aptitudes = [];
            foreach ($spec['levels'] as $level => $data) {
                $caps = $data['capacities'] ?? [];
                $types = array_count_values(array_column($caps, 'type'));
                $this->assertGreaterThanOrEqual(1, $types['garantie'] ?? 0, $name.' palier '.$level.' sans garantie.');
                $this->assertSame(2, $types['choix'] ?? 0, $name.' palier '.$level.' doit avoir 2 options.');

                foreach ($data['aptitudes'] ?? [] as $aptitude) {
                    $aptitudes[(int) $level] = $aptitude['name'] ?? '';
                }
            }

            $this->assertSame([3, 9, 15], array_keys($aptitudes), $name.' : aptitudes hors 3 / 9 / 15.');
        }
    }
}
