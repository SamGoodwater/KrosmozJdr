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
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('effect')->nullable();
            $table->integer('level')->default(1);
            $table->string('pa')->nullable();
            $table->string('po')->default('1');
            $table->boolean('po_editable')->default(true);
            $table->string('time_before_use_again')->nullable();
            $table->string('casting_time')->nullable();
            $table->string('duration')->nullable();
            $table->integer('element')->default(0);
            $table->boolean('is_magic')->default(true);
            $table->boolean('ritual_available')->default(true);
            $table->integer('powerful')->default(2);
            $table->boolean('usable')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capabilities');
        Schema::table('capabilities', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
    }
};
