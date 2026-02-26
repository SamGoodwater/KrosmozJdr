<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('effect_sub_effect', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effect_id')->constrained('effects')->cascadeOnDelete();
            $table->foreignId('sub_effect_id')->constrained('sub_effects')->cascadeOnDelete();
            $table->unsignedSmallInteger('order')->default(0);
            $table->string('scope', 32)->default('general');
            $table->integer('value_min')->nullable();
            $table->integer('value_max')->nullable();
            $table->unsignedTinyInteger('dice_num')->nullable();
            $table->unsignedTinyInteger('dice_side')->nullable();
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index(['effect_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_sub_effect');
    }
};
