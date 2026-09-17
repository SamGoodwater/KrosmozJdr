<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('object_effects', function (Blueprint $table) {
            $table->id();
            $table->morphs('object_effectable');
            $table->string('action', 32);
            $table->foreignId('characteristic_id')->nullable()->constrained('characteristics')->nullOnDelete();
            $table->foreignId('monster_id')->nullable()->constrained('monsters')->nullOnDelete();
            $table->integer('value')->nullable();
            $table->timestamps();

            $table->index(['action', 'characteristic_id']);
            $table->index(['action', 'monster_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('object_effects');
    }
};
