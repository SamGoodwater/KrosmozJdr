<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aptitudes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('effect')->nullable();

            // Phrases libres (réutilisation, durée, portée)
            $table->text('reuse')->nullable();
            $table->text('duration')->nullable();
            $table->text('range')->nullable();

            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->index('name');
            $table->index('state');
        });

        Schema::create('aptitude_specialization', function (Blueprint $table) {
            $table->foreignId('aptitude_id')->constrained('aptitudes')->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->primary(['aptitude_id', 'specialization_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aptitude_specialization');
        Schema::dropIfExists('aptitudes');
    }
};
