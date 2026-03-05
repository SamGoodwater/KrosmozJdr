<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_filter_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('entity_type', 120);
            $table->string('table_id', 191)->nullable();
            $table->string('name', 120);
            $table->text('search_text')->nullable();
            $table->json('filters')->nullable();
            $table->unsignedSmallInteger('limit')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'entity_type']);
            $table->index(['user_id', 'entity_type', 'table_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_filter_presets');
    }
};

