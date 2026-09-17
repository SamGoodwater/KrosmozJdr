<?php

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesMarkdownCharacteristicKrefAutowrap;
use PHPUnit\Framework\TestCase;

class RulesMarkdownCharacteristicKrefAutowrapTest extends TestCase
{
    public function test_wraps_plain_elements_line(): void
    {
        $md = '> **Éléments** : Force (Terre), Agilité (Air), Intelligence (Feu), Chance (Eau)';
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertStringContainsString('[[kref:characteristic:strength_creature|Force]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:agility_creature|Agilité]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:intelligence_creature|Intelligence]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:chance_creature|Chance]]', $out);
    }

    public function test_does_not_duplicate_existing_shortcode_label(): void
    {
        $md = 'Voir [[kref:characteristic:strength_creature|Force]] pour détails.';
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertSame(1, substr_count($out, '[[kref:characteristic:strength_creature|Force]]'));
    }

    public function test_still_wraps_force_outside_shortcode(): void
    {
        $md = '**Force** : test';
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertStringContainsString('[[kref:characteristic:strength_creature|Force]]', $out);
    }

    /**
     * Les cellules GFM sans espace après le pipe (`|Force|`) ne doivent pas bloquer la conversion
     * (régression : un lookbehind sur {@code |} était inutile une fois les shortcodes kref masqués).
     */
    public function test_wraps_characteristics_in_markdown_table_cells_after_pipe(): void
    {
        $md = "| Carac | Mod |\n|-------|-----|\n|Force|+2|\n|Agilité|+1|\n|Intelligence|0|\n|Sagesse|-1|";
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertStringContainsString('[[kref:characteristic:strength_creature|Force]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:agility_creature|Agilité]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:intelligence_creature|Intelligence]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:wisdom_creature|Sagesse]]', $out);
    }

    public function test_wraps_modifier_and_save_phrases(): void
    {
        $md = 'modificateur de Vitalité et sauvegarde de Force ; Jet de sauvegarde d’Intelligence.';
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertStringContainsString('[[kref:characteristic:modifier_vitality_creature|modificateur de Vitalité]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:save_strength_creature|sauvegarde de Force]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:save_intelligence_creature|Jet de sauvegarde d’Intelligence]]', $out);
    }

    public function test_wraps_resistance_and_equipment_bonus_phrases(): void
    {
        $md = 'Résistance Eau % et Résistance fixe Terre ; bonus d’équipement du tacle.';
        $out = RulesMarkdownCharacteristicKrefAutowrap::apply($md);
        $this->assertStringContainsString('[[kref:characteristic:resistance_water_creature|Résistance Eau %]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:fixed_resistance_earth_creature|Résistance fixe Terre]]', $out);
        $this->assertStringContainsString('[[kref:characteristic:tackle_object|bonus d’équipement du tacle]]', $out);
    }
}
