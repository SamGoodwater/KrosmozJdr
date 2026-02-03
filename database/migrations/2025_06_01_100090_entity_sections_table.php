<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->integer('order')->default(0);
            $table->string('template');
            $table->string('type')->nullable();
            $table->json('settings')->nullable();
            $table->json('data')->nullable();
            $table->json('params')->nullable();
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(4);
            $table->string('state')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
