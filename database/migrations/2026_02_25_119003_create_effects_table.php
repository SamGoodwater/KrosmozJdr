<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug', 64)->nullable()->unique();
            $table->text('description')->nullable();
            $table->foreignId('effect_group_id')->nullable()->constrained('effect_groups')->nullOnDelete();
            $table->unsignedTinyInteger('degree')->nullable();
            $table->timestamps();

            $table->index(['effect_group_id', 'degree']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effects');
    }
};
