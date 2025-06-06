<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->timestamps();
            $table->string('name');
            $table->string('description_fast')->nullable();
            $table->string('description')->nullable();
            $table->string('life')->nullable();
            $table->string('life_dice')->nullable();
            $table->string('specificity')->nullable();
            $table->string('dofus_version')->default('3');
            $table->tinyInteger('usable')->default(0);
            $table->string('is_visible')->default('guest');
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
