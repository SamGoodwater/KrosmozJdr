<?php

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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->integer('order')->default(0);
            $table->string('template');
            $table->json('settings')->nullable();
            $table->json('data')->nullable();
            $table->string('is_visible')->default('guest');
            $table->string('can_edit_role')->default('admin');
            $table->string('state')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
