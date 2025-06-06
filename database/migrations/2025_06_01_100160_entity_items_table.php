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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('name');
            $table->string('level')->nullable();
            $table->string('description')->nullable();
            $table->string('effect')->nullable();
            $table->string('bonus')->nullable();
            $table->string('recipe')->nullable();
            $table->string('price')->nullable();
            $table->integer('rarity')->default(0);
            $table->string('dofus_version')->default('3');
            $table->tinyInteger('usable')->default(0);
            $table->string('is_visible')->default('guest');
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();
            $table->foreignId('item_type_id')->nullable()->constrained('item_types')->cascadeOnDelete();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
