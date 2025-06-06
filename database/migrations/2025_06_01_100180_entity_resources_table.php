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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('dofusdb_id')->nullable();
            $table->integer('official_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('level')->default('1');
            $table->string('price')->nullable();
            $table->string('weight')->nullable();
            $table->integer('rarity')->default(0);
            $table->string('dofus_version')->default('3');
            $table->tinyInteger('usable')->default(0);
            $table->string('is_visible')->default('guest');
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();
            $table->foreignId('resource_type_id')->nullable()->constrained('resource_types')->cascadeOnDelete();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
