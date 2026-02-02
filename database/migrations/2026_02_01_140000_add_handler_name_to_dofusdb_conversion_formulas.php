<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute handler_name pour appeler une fonction PHP complexe (ex. résistances Dofus → Krosmoz).
     * Si renseigné, la conversion utilise ce handler au lieu de formula_type / conversion_formula.
     *
     * @see docs/50-Fonctionnalités/Characteristics-DB/CONVERSION_100_BDD_ET_HANDLERS.md
     */
    public function up(): void
    {
        Schema::table('dofusdb_conversion_formulas', function (Blueprint $table) {
            $table->string('handler_name', 64)->nullable()->after('conversion_formula');
        });
    }

    public function down(): void
    {
        Schema::table('dofusdb_conversion_formulas', function (Blueprint $table) {
            $table->dropColumn('handler_name');
        });
    }
};
