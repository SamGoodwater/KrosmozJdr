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

            // Réutilisation
            $table->string('reuse_type', 32)->default('unlimited');
            $table->unsignedInteger('reuse_amount')->nullable();
            $table->string('reuse_unit', 16)->nullable();

            // Durée d'effet
            $table->string('duration_type', 32)->default('permanent');
            $table->unsignedInteger('duration_amount')->nullable();
            $table->string('duration_unit', 16)->nullable();
            $table->string('duration_note')->nullable();

            // Portée
            $table->string('range_type', 32)->default('self');
            $table->unsignedInteger('range_amount')->nullable();
            $table->string('range_unit', 8)->nullable();
            $table->string('range_sense', 32)->nullable();
            $table->string('range_note')->nullable();

            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->index('name');
            $table->index('state');
            $table->index('reuse_type');
            $table->index('duration_type');
            $table->index('range_type');
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
