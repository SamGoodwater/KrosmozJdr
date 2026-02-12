<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characteristics', function (Blueprint $table): void {
            $table->string('group', 16)
                ->nullable()
                ->after('sort_order')
                ->comment('Groupe principal : creature, object ou spell');

            $table->foreignId('linked_to_characteristic_id')
                ->nullable()
                ->after('group')
                ->constrained('characteristics')
                ->nullOnDelete()
                ->comment('Caractéristique maître si cette ligne est une caractéristique liée');
        });

        // Rétro-remplissage du champ group pour les lignes existantes
        DB::table('characteristics')
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    if ($row->group !== null) {
                        continue;
                    }

                    $group = null;
                    $id = (int) $row->id;

                    if (DB::table('characteristic_creature')->where('characteristic_id', $id)->exists()) {
                        $group = 'creature';
                    } elseif (DB::table('characteristic_object')->where('characteristic_id', $id)->exists()) {
                        $group = 'object';
                    } elseif (DB::table('characteristic_spell')->where('characteristic_id', $id)->exists()) {
                        $group = 'spell';
                    }

                    // Fallback : si aucune ligne trouvée, on met creature par défaut (comportement actuel)
                    if ($group === null) {
                        $group = 'creature';
                    }

                    DB::table('characteristics')
                        ->where('id', $id)
                        ->update(['group' => $group]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('characteristics', function (Blueprint $table): void {
            $table->dropForeign(['linked_to_characteristic_id']);
            $table->dropColumn(['linked_to_characteristic_id', 'group']);
        });
    }
};

