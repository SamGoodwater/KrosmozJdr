<?php

declare(strict_types=1);

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
        Schema::create('equipment_slot_characteristics', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_slot_id', 32);
            $table->string('characteristic_id', 64);
            $table->json('bracket_max');
            $table->unsignedTinyInteger('forgemagie_max')->nullable();
            $table->decimal('base_price_per_unit', 12, 2)->nullable();
            $table->decimal('rune_price_per_unit', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('equipment_slot_id')->references('id')->on('equipment_slots')->cascadeOnDelete();
            $table->foreign('characteristic_id')->references('id')->on('characteristics')->cascadeOnDelete();
            $table->unique(['equipment_slot_id', 'characteristic_id'], 'equip_slot_char_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_slot_characteristics');
    }
};
