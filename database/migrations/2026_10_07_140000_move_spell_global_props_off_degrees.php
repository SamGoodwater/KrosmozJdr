<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * element, target_type et ritual_available appartiennent au sort, pas aux degrés.
 * Rétro-copie depuis le degré 1 puis suppression des colonnes sur spell_degrees.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spell_degrees') || ! Schema::hasTable('spells')) {
            return;
        }

        $hasElement = Schema::hasColumn('spell_degrees', 'element');
        $hasTargetType = Schema::hasColumn('spell_degrees', 'target_type');
        $hasRitual = Schema::hasColumn('spell_degrees', 'ritual_available');

        if (! $hasElement && ! $hasTargetType && ! $hasRitual) {
            return;
        }

        $degrees = DB::table('spell_degrees')
            ->where('position', 1)
            ->orderBy('id')
            ->get();

        foreach ($degrees as $degree) {
            $updates = [];
            $spell = DB::table('spells')->where('id', $degree->spell_id)->first();
            if ($spell === null) {
                continue;
            }

            if ($hasElement && $spell->element === null && $degree->element !== null) {
                $updates['element'] = $degree->element;
            }
            if ($hasTargetType
                && ($spell->target_type === null || $spell->target_type === '')
                && $degree->target_type !== null
                && $degree->target_type !== ''
            ) {
                $updates['target_type'] = $degree->target_type;
            }
            if ($hasRitual && $spell->ritual_available === null && $degree->ritual_available !== null) {
                $updates['ritual_available'] = (bool) $degree->ritual_available;
            }

            if ($updates !== []) {
                DB::table('spells')->where('id', $degree->spell_id)->update($updates);
            }
        }

        Schema::table('spell_degrees', function (Blueprint $table) use ($hasElement, $hasTargetType, $hasRitual) {
            if ($hasTargetType) {
                $table->dropColumn('target_type');
            }
            if ($hasElement) {
                $table->dropColumn('element');
            }
            if ($hasRitual) {
                $table->dropColumn('ritual_available');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('spell_degrees')) {
            return;
        }

        Schema::table('spell_degrees', function (Blueprint $table) {
            if (! Schema::hasColumn('spell_degrees', 'target_type')) {
                $table->string('target_type', 16)->nullable()->after('cast_in_diagonal');
            }
            if (! Schema::hasColumn('spell_degrees', 'element')) {
                $table->integer('element')->nullable()->after('target_type');
            }
            if (! Schema::hasColumn('spell_degrees', 'ritual_available')) {
                $table->boolean('ritual_available')->nullable()->after('casting_time');
            }
        });
    }
};
