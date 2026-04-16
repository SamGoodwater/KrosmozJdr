<?php

declare(strict_types=1);

use App\Models\Characteristic;
use Illuminate\Database\Migrations\Migration;

/**
 * Supprime les caractéristiques objet agrégées remplacées par des clés distinctes
 * (sauvegardes ×6, compétences passives ×18). CASCADE sur characteristic_object.
 */
return new class extends Migration
{
    private const DEPRECATED_KEYS = [
        'passive_skills_object',
        'save_strength_intelligence_chance_agility_object',
        'save_vitality_wisdom_object',
    ];

    public function up(): void
    {
        foreach (self::DEPRECATED_KEYS as $key) {
            Characteristic::query()->where('key', $key)->delete();
        }
    }

    public function down(): void
    {
        // Réintroduction manuelle via CharacteristicSeeder si rollback nécessaire.
    }
};
