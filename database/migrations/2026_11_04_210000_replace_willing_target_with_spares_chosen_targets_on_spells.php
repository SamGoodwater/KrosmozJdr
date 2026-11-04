<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Remplace « réussite auto si cible consentante » par « Épargne » (règle 3.3.2.3).
 *
 * @description
 * Le consentement est devenu une règle générale : l’ancien drapeau ne porte plus d’information,
 * ses valeurs ne sont pas reprises. `spares_chosen_targets` : le lanceur choisit des créatures
 * qui ne subissent pas les dégâts du sort mais reçoivent ses autres effets.
 */
return new class extends Migration
{
    private const OLD_COLUMN = 'auto_success_if_willing_target';

    private const NEW_COLUMN = 'spares_chosen_targets';

    private const OLD_KEY = 'auto_success_if_willing_target_spell';

    private const NEW_KEY = 'spares_chosen_targets_spell';

    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->renameColumn(self::OLD_COLUMN, self::NEW_COLUMN);
        });
        Schema::table('spell_degrees', function (Blueprint $table): void {
            $table->renameColumn(self::OLD_COLUMN, self::NEW_COLUMN);
        });

        DB::table('spells')->update([self::NEW_COLUMN => false]);
        DB::table('spell_degrees')->update([self::NEW_COLUMN => null]);

        $this->renameCharacteristic(self::OLD_KEY, self::NEW_KEY, self::OLD_COLUMN, self::NEW_COLUMN, [
            'name' => 'Épargne',
            'short_name' => 'Épargne',
            'helper' => 'Le lanceur choisit des créatures touchées qui ne subissent aucun dégât du sort ; elles reçoivent ses autres effets.',
            'descriptions' => 'Booléen (`spells.spares_chosen_targets`). Règle 3.3.2.3 — Épargne.',
            'hide_when_false' => true,
            'value_overrides' => json_encode([
                ['value' => true, 'color' => 'emerald', 'subtitle' => 'Épargne : les créatures choisies par le lanceur ne subissent pas les dégâts.', 'icon' => 'success.webp'],
                ['value' => false, 'color' => 'neutral', 'subtitle' => 'Le sort inflige ses dégâts à toutes les créatures touchées.'],
            ], JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->renameColumn(self::NEW_COLUMN, self::OLD_COLUMN);
        });
        Schema::table('spell_degrees', function (Blueprint $table): void {
            $table->renameColumn(self::NEW_COLUMN, self::OLD_COLUMN);
        });

        $this->renameCharacteristic(self::NEW_KEY, self::OLD_KEY, self::NEW_COLUMN, self::OLD_COLUMN, [
            'name' => 'Réussite auto si cible consentante',
            'short_name' => 'Auto si volontaire',
            'helper' => 'Si vrai, la cible consentante réussit automatiquement le jet concerné (sauvegarde ou effet).',
            'descriptions' => 'Booléen (`spells.auto_success_if_willing_target`). Hors périmètre conversion Dofus standard.',
            'hide_when_false' => false,
            'value_overrides' => json_encode([
                ['value' => true, 'color' => 'emerald', 'subtitle' => 'Réussite automatique si la cible est consentante.', 'icon' => 'success.webp'],
                ['value' => false, 'color' => 'neutral', 'subtitle' => 'Aucune réussite automatique liée au consentement.'],
            ], JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * @param  array<string, string|bool>  $labels
     */
    private function renameCharacteristic(string $fromKey, string $toKey, string $fromColumn, string $toColumn, array $labels): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        $id = DB::table('characteristics')->where('key', $fromKey)->value('id');
        if ($id === null) {
            return;
        }
        DB::table('characteristics')->where('id', $id)->update(['key' => $toKey] + $labels);
        if (Schema::hasTable('characteristic_spell')) {
            DB::table('characteristic_spell')
                ->where('characteristic_id', $id)
                ->where('db_column', $fromColumn)
                ->update(['db_column' => $toColumn]);
        }
    }
};
