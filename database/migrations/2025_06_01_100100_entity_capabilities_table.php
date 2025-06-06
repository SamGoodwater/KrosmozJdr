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
        Schema::create('capabilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('effect')->nullable();
            $table->string('level')->default('1');
            $table->string('pa')->default('3');
            $table->string('po')->default('0');
            $table->boolean('po_editable')->default(true);
            $table->string('time_before_use_again')->default('0');
            $table->string('casting_time')->default('0');
            $table->string('duration')->default('0');
            $table->string('element')->default('neutral');
            $table->boolean('is_magic')->default(true);
            $table->boolean('ritual_available')->default(true);
            $table->string('powerful')->nullable();
            $table->tinyInteger('usable')->default(0);
            $table->string('is_visible')->default('guest');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capabilities');
    }
};
