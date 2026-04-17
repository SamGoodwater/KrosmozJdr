<?php

declare(strict_types=1);

use App\Models\Characteristic;
use Illuminate\Database\Migrations\Migration;

/**
 * Supprime les caractéristiques objet redondantes : agrégat « compétences » et anciennes
 * résistances % globales (remplacées par les compétences individuelles et resistance_percent_tier_*_object).
 * CASCADE sur characteristic_object.
 */
return new class extends Migration
{
    private const DEPRECATED_KEYS = [
        'skills_object',
        'resistance_50_percent_object',
        'invulnerability_100_percent_object',
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
