<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la colonne conversion_formula pour stocker une formule simple (string)
     * ou une table JSON (comme pour les formules des characteristic_entities).
     * Variable d'entrée : [d] = valeur DofusDB ; [level] = niveau JDR (pour vie).
     */
    public function up(): void
    {
        Schema::table('dofusdb_conversion_formulas', function (Blueprint $table) {
            $table->text('conversion_formula')->nullable()->after('formula_type');
        });
    }

    public function down(): void
    {
        Schema::table('dofusdb_conversion_formulas', function (Blueprint $table) {
            $table->dropColumn('conversion_formula');
        });
    }
};
