<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dofusdb_conversion_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('characteristic_key', 64);
            $table->string('entity', 32);
            $table->string('formula_type', 64);
            $table->text('conversion_formula')->nullable();
            $table->string('handler_name', 64)->nullable();
            $table->text('formula_display')->nullable();
            $table->json('parameters')->nullable();
            $table->timestamps();

            $table->unique(['characteristic_key', 'entity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dofusdb_conversion_formulas');
    }
};
