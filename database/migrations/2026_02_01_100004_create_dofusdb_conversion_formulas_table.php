<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Formules de conversion DofusDB → KrosmozJDR par (characteristic_id, entity).
     * Permet de stocker en BDD les formules (type + paramètres) pour le scrapping.
     */
    public function up(): void
    {
        Schema::create('dofusdb_conversion_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('characteristic_id');
            $table->string('entity', 32);
            $table->string('formula_type', 64);
            $table->json('parameters')->nullable();
            $table->text('formula_display')->nullable();
            $table->timestamps();

            $table->unique(['characteristic_id', 'entity']);
            $table->foreign('characteristic_id')
                ->references('id')
                ->on('characteristics')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dofusdb_conversion_formulas');
    }
};
