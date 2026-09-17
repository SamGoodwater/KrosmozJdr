<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_effects', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('type_slug', 64)->index();
            $table->text('template_text')->nullable();
            $table->text('formula')->nullable();
            $table->json('variables_allowed')->nullable();
            $table->json('param_schema')->nullable();
            $table->unsignedInteger('dofusdb_effect_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_effects');
    }
};
