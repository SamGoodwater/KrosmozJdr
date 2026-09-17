<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condition_capability', function (Blueprint $table): void {
            $table->foreignId('condition_id')->constrained('conditions')->cascadeOnDelete();
            $table->foreignId('capability_id')->constrained('capabilities')->cascadeOnDelete();
            $table->primary(['condition_id', 'capability_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condition_capability');
    }
};
