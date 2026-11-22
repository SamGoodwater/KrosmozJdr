<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use Tests\TestCase;

/**
 * Fiches jouables : gabarit legacy 2.4.2.6, ou enveloppe 2.4.6 (Artiste essai).
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

    /**
     * @param  array<string, mixed>  $spec
     */
    private function isEnvelope(array $spec): bool
    {
        return ($spec['envelope'] ?? '') === '2.4.6'
            || (is_array($spec['progression'] ?? null) && $spec['progression'] !== []);
    }

    public function test_all_playable_sheets_share_the_same_gabarit(): void
    {
        $this->assertCount(11, $this->sheetPaths());

        foreach ($this->sheetPaths() as $path) {
            $spec = require $path;
            $name = (string) ($spec['name'] ?? basename($path));
            $this->assertTrue($spec['playable'] ?? false, $name);
            $this->assertSame([1, 3, 6, 9, 12, 15, 20], array_keys($spec['levels'] ?? []), $name);

            if ($this->isEnvelope($spec)) {
                $this->assertEnvelopeSheet($spec, $name);

                continue;
            }

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

    /**
     * @param  array<string, mixed>  $spec
     */
    private function assertEnvelopeSheet(array $spec, string $name): void
    {
        $this->assertArrayHasKey('progression', $spec, $name);
        $this->assertSame(
            [1, 3, 6, 9, 12, 15, 20],
            array_map('intval', array_keys($spec['progression'])),
            $name.' : progression incomplète.'
        );

        $styles = $spec['styles'] ?? $spec['archetypes'] ?? [];
        $this->assertIsArray($styles);
        $this->assertNotEmpty($styles, $name.' : styles manquants.');
        $this->assertSame(3, (int) ($spec['styles_at'] ?? 3), $name.' : styles au niveau 3.');

        $skillsLine = (string) ($spec['levels'][1]['masteries']['Compétences'] ?? '');
        $found = [];
        foreach (self::SKILL_NAMES as $skill) {
            if (str_contains($skillsLine, $skill)) {
                $found[] = $skill;
            }
        }
        $this->assertGreaterThanOrEqual(2, count($found), $name.' : liste de compétences niv. 1 trop courte.');

        $this->assertTrue(
            (bool) ($spec['levels'][3]['unlock_style'] ?? false),
            $name.' : unlock_style attendu au niveau 3.'
        );

        foreach ([3, 9, 15] as $level) {
            $variantCaps = array_values(array_filter(
                $spec['levels'][$level]['capacities'] ?? [],
                static fn (array $cap): bool => isset($cap['archetype_variants'])
            ));
            $this->assertNotEmpty($variantCaps, $name.' palier '.$level.' : variante de style attendue.');
            $this->assertSame(
                array_keys($styles),
                array_keys($variantCaps[0]['archetype_variants']),
                $name.' palier '.$level.' : variantes ≠ styles.'
            );
        }
    }

    public function test_wakfu_costs_are_written_in_pa_from_the_reserve(): void
    {
        foreach ($this->sheetPaths() as $path) {
            $spec = require $path;
            foreach ($spec['levels'] as $level => $data) {
                foreach ($data['capacities'] ?? [] as $cap) {
                    $label = basename($path).' palier '.$level.' — '.($cap['name'] ?? '?');
                    $effect = (string) ($cap['effect'] ?? '');
                    $this->assertDoesNotMatchRegularExpression('/\d+\s+réserves?\s+de\s+Wakfu/u', $effect, $label);

                    if (preg_match('/(\d+)\s+PA\s+depuis\s+la\s+réserve\s+de\s+Wakfu/u', $effect, $m) === 1) {
                        $this->assertSame($m[1], (string) ($cap['pa'] ?? ''), $label.' : champ pa incohérent.');
                    }

                    foreach ($cap['archetype_variants'] ?? [] as $slug => $variant) {
                        $vEffect = (string) ($variant['effect'] ?? '');
                        $vLabel = $label.' / '.$slug;
                        $this->assertDoesNotMatchRegularExpression('/\d+\s+réserves?\s+de\s+Wakfu/u', $vEffect, $vLabel);
                        if (preg_match('/(\d+)\s+PA\s+depuis\s+la\s+réserve\s+de\s+Wakfu/u', $vEffect, $m) === 1) {
                            $this->assertSame($m[1], (string) ($variant['pa'] ?? ''), $vLabel.' : champ pa incohérent.');
                        }
                    }
                }
            }
        }
    }
}
