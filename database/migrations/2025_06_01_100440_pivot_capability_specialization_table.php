<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capability_specialization', function (Blueprint $table) {
            $table->foreignId('capability_id')->constrained('capabilities')->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->primary(['capability_id', 'specialization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capability_specialization');
    }
};
