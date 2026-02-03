<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('effect')->nullable();
            $table->string('level')->nullable();
            $table->string('recipe')->nullable();
            $table->string('price')->nullable();
            $table->integer('rarity')->default(0);
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->string('dofus_version')->default('3');
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();
            $table->foreignId('consumable_type_id')->nullable()->constrained('consumable_types')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};
