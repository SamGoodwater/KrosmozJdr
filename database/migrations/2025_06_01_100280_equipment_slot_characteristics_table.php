<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_slot_characteristics', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_slot_id', 32);
            $table->string('entity', 32)->default('item');
            $table->string('characteristic_key', 64);
            $table->json('bracket_max');
            $table->unsignedTinyInteger('forgemagie_max')->nullable();
            $table->decimal('base_price_per_unit', 12, 2)->nullable();
            $table->decimal('rune_price_per_unit', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('equipment_slot_id')->references('id')->on('equipment_slots')->cascadeOnDelete();
            $table->unique(['equipment_slot_id', 'entity', 'characteristic_key'], 'equip_slot_entity_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_slot_characteristics');
    }
};
