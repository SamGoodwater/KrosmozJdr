<?php

use App\Models\Modules\Classe;
use App\Models\Modules\Creature;
use App\Models\Modules\Specialization;
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
        Schema::create('npcs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Modules\Creature::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('story')->nullable();
            $table->string('historical')->nullable();
            $table->string('age')->nullable();
            $table->string('size')->nullable();
            $table->foreignIdFor(\App\Models\Modules\Classe::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Specialization::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npcs');
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeignIdFor(Specialization::class);
        });
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeignIdFor(Classe::class);
        });
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeignIdFor(Creature::class);
        });
    }
};
