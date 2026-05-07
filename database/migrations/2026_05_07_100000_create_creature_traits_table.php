<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creature_traits', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->text('description')->nullable(); $table->string('state')->default('draft'); $table->tinyInteger('read_level')->default(0); $table->tinyInteger('write_level')->default(3); $table->string('image')->nullable(); $table->timestamps(); $table->softDeletes(); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('creature_traits'); }
};
