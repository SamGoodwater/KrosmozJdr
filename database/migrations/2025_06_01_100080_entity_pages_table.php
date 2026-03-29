<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(4);
            $table->string('state')->default('draft');
            $table->boolean('in_menu')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->integer('menu_order')->default(0);
            $table->string('menu_group')->nullable();
            $table->string('entity_key', 50)->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('page_css_classes', 500)->nullable();
            $table->string('title_css_classes', 500)->nullable();
            $table->string('menu_item_css_classes', 500)->nullable();
            $table->index('menu_group');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
