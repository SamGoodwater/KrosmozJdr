<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes pour les bonus d'équipement (+0 à +3) et maîtrise (0/1)
 * des jets de sauvegarde des créatures.
 *
 * Formule (règles 2.2.2) : 1d20 + mod. carac. + bonus maîtrise (si maîtrisé) + bonus équipement
 * Max : 7 + 6 + 3 = 16
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatures', function (Blueprint $table) {
            // Bonus équipement (chapeaux/capes) : 0 à 3 par sauvegarde
            $table->unsignedTinyInteger('save_vitality_bonus')->default(0)->after('supercherie_mastery');
            $table->unsignedTinyInteger('save_wisdom_bonus')->default(0)->after('save_vitality_bonus');
            $table->unsignedTinyInteger('save_strength_bonus')->default(0)->after('save_wisdom_bonus');
            $table->unsignedTinyInteger('save_intelligence_bonus')->default(0)->after('save_strength_bonus');
            $table->unsignedTinyInteger('save_chance_bonus')->default(0)->after('save_intelligence_bonus');
            $table->unsignedTinyInteger('save_agility_bonus')->default(0)->after('save_chance_bonus');

            // Maîtrise de la sauvegarde (0 = non, 1 = oui → +bonus maîtrise)
            $table->unsignedTinyInteger('save_vitality_mastery')->default(0)->after('save_agility_bonus');
            $table->unsignedTinyInteger('save_wisdom_mastery')->default(0)->after('save_vitality_mastery');
            $table->unsignedTinyInteger('save_strength_mastery')->default(0)->after('save_wisdom_mastery');
            $table->unsignedTinyInteger('save_intelligence_mastery')->default(0)->after('save_strength_mastery');
            $table->unsignedTinyInteger('save_chance_mastery')->default(0)->after('save_intelligence_mastery');
            $table->unsignedTinyInteger('save_agility_mastery')->default(0)->after('save_chance_mastery');
        });
    }

    public function down(): void
    {
        Schema::table('creatures', function (Blueprint $table) {
            $table->dropColumn([
                'save_vitality_bonus',
                'save_wisdom_bonus',
                'save_strength_bonus',
                'save_intelligence_bonus',
                'save_chance_bonus',
                'save_agility_bonus',
                'save_vitality_mastery',
                'save_wisdom_mastery',
                'save_strength_mastery',
                'save_intelligence_mastery',
                'save_chance_mastery',
                'save_agility_mastery',
            ]);
        });
    }
};
