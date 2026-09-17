<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('slug');
            $table->string('keyword')->nullable();
            $table->boolean('is_public')->default(false);
            $table->integer('progress_state')->default(0);
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};
