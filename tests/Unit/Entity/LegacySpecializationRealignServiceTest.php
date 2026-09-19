<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Services\Entity\LegacyEntitySectionImportService;
use App\Services\Entity\LegacySpecializationRealignService;
use Tests\TestCase;

/**
 * Garde-fous du redécoupage des spécialisations legacy sur les paliers de 2.4.2.
 */
final class LegacySpecializationRealignServiceTest extends TestCase
{
    private LegacySpecializationRealignService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $importer = $this->createMock(LegacyEntitySectionImportService::class);
        $importer->method('buildCapabilityKrefListHtml')
            ->willReturnCallback(static fn (array $names): string => '<ul><li>'.implode('</li><li>', $names).'</li></ul>');

        $this->service = new LegacySpecializationRealignService($importer);
    }

    public function test_it_maps_the_legacy_grid_onto_the_seven_paliers(): void
    {
        $sections = $this->service->realign($this->legacySections(), []);
        $levels = array_values(array_unique(array_map(static fn (array $s): int => $s['level'], $sections)));

        $this->assertSame([1, 3, 6, 9, 12, 15, 20], $levels);
    }

    public function test_it_merges_capability_lists_of_merged_paliers(): void
    {
        $sections = $this->service->realign($this->legacySections(), []);
        $capabilities = [];
        foreach ($sections as $section) {
            if (isset($section['capabilities']) && $section['level'] === 15) {
                $capabilities = $section['capabilities'];
            }
        }

        $this->assertSame(['Contingence', 'Téléportation'], $capabilities);
    }

    public function test_it_keeps_only_the_planned_aptitudes_and_demotes_the_others(): void
    {
        $sections = $this->service->realign($this->legacySections(), [
            'aptitudes' => ['Politicien' => 3],
        ]);

        $byLevel = [];
        foreach ($sections as $section) {
            if (! isset($section['capabilities'])) {
                $byLevel[$section['level']] = $section['content'];
            }
        }

        $this->assertStringContainsString('<h2>Aptitude (automatique)</h2><h3>Politicien</h3>', $byLevel[3]);
        $this->assertStringNotContainsString('Aptitude (automatique)', $byLevel[9]);
        $this->assertStringContainsString('<h2>Capacités</h2>', $byLevel[9]);
        $this->assertStringContainsString('Façonneur de sorts', $byLevel[9]);
    }

    public function test_it_moves_an_aptitude_to_its_planned_palier(): void
    {
        $sections = $this->service->realign($this->legacySections(), [
            'aptitudes' => ['Façonneur de sorts' => 15],
        ]);

        $byLevel = [];
        foreach ($sections as $section) {
            if (! isset($section['capabilities'])) {
                $byLevel[$section['level']] = $section['content'];
            }
        }

        $this->assertStringContainsString('<h2>Aptitude (automatique)</h2><h3>Façonneur de sorts</h3>', $byLevel[15]);
        $this->assertArrayNotHasKey(9, $byLevel, 'Le palier 9 ne contenait que ce bonus : il ne reste rien à publier.');
    }

    public function test_it_renames_legacy_aptitudes_into_capacites(): void
    {
        $sections = $this->service->realign($this->legacySections(), []);
        $content = '';
        foreach ($sections as $section) {
            if (! isset($section['capabilities']) && $section['level'] === 3) {
                $content = $section['content'];
            }
        }

        $this->assertStringContainsString('Capacités proposées à ce palier :', $content);
        $this->assertStringNotContainsString('aptitudes au choix', $content);
        $this->assertStringNotContainsString('<h2>Aptitudes</h2>', $content);
    }

    public function test_it_remaps_level_references_inside_the_text(): void
    {
        $sections = $this->service->realign($this->legacySections(), []);
        $content = '';
        foreach ($sections as $section) {
            if (! isset($section['capabilities']) && $section['level'] === 9) {
                $content = $section['content'];
            }
        }

        $this->assertStringContainsString('niveau 9 : 1 créature', $content);
        $this->assertStringNotContainsString('niveau 8', $content);
    }

    public function test_every_legacy_specialization_has_three_aptitudes_on_the_right_paliers(): void
    {
        $plans = require dirname(__DIR__, 3).'/database/seeders/data/legacy-specialization-realignment.php';

        $this->assertSame(
            ['artiste', 'devot', 'erudit', 'explorateur_rice', 'milicien_ne', 'voleur_euse'],
            array_keys($plans),
        );

        foreach ($plans as $slug => $plan) {
            $paliers = array_merge(
                array_values($plan['aptitudes'] ?? []),
                array_keys($plan['authored'] ?? []),
            );
            sort($paliers);

            $this->assertSame(
                LegacySpecializationRealignService::APTITUDE_PALIERS,
                $paliers,
                "La spécialisation {$slug} doit porter exactement une aptitude aux paliers 3, 9 et 15.",
            );
        }
    }

    /**
     * Extrait représentatif d'un export legacy, déjà passé par `parseLegacySections`.
     *
     * @return list<array{title: string, level: int, content: string, capabilities?: list<string>}>
     */
    private function legacySections(): array
    {
        return [
            ['title' => 'Texte', 'level' => 1, 'content' => '<p>Présentation.</p>'],
            ['title' => 'Niveau 1', 'level' => 1, 'content' => '<h2>Maitrises</h2><p>Deux compétences.</p><h2>Aptitudes</h2><p>Choisissez deux aptitudes parmi les suivantes :</p><h2>Capacités</h2>'],
            ['title' => 'Niveau 3', 'level' => 3, 'content' => '<h2>Aptitudes</h2><p>Deux aptitudes au choix entre :</p><h2>Capacités</h2><h4>Politicien</h4><p>Avantage en haute société.</p>'],
            ['title' => 'Niveau 5', 'level' => 5, 'content' => '<h2>Aptitudes</h2><p>Une aptitude au choix entre :</p>'],
            ['title' => 'Niveau 8', 'level' => 8, 'content' => '<h2>Capacités</h2><h4>Façonneur de sorts</h4><p>Au niveau 8 : 1 créature.</p>'],
            ['title' => 'Niveau 10', 'level' => 10, 'content' => '<h2>Aptitudes</h2><p>Une aptitude au choix entre :</p>'],
            ['title' => 'Niveau 13', 'level' => 13, 'content' => '<h2>Aptitudes</h2><p>Deux aptitudes au choix entre :</p><h2>Maitrises</h2><p>Devenir expert dans 1 compétence.</p>'],
            ['title' => 'Liste d\'Aptitudes', 'level' => 13, 'content' => '', 'capabilities' => ['Contingence']],
            ['title' => 'Niveau 15', 'level' => 15, 'content' => '<h2>Aptitudes</h2><p>Une aptitude au choix entre :</p>'],
            ['title' => 'Liste d\'Aptitudes', 'level' => 15, 'content' => '', 'capabilities' => ['Téléportation']],
            ['title' => 'Niveau 18', 'level' => 18, 'content' => '<h2>Aptitudes</h2><p>Une aptitude au choix entre :</p>'],
            ['title' => 'Niveau 20', 'level' => 20, 'content' => '<h2>Aptitudes</h2><p>Une aptitude au choix entre :</p><h2>Maitrises</h2><p>Devenir expert dans 1 compétence.</p>'],
        ];
    }
}
