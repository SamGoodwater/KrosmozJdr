<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Modules\Creature::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('dofus_version')->default('3');
            $table->boolean('auto_update')->default(true);
            $table->integer('size')->default(2);
            $table->foreignIdFor(\App\Models\Modules\MobRace::class)->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobs');
        Schema::table('mobs', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Modules\Creature::class);
        });
        Schema::table('mobs', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Modules\MobRace::class);
        });
    }
};
