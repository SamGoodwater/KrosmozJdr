<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monster_races', function (Blueprint $table) {
            $table->id();
            $table->integer('dofusdb_race_id')->nullable()->unique();
            $table->string('name');
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('id_super_race')->nullable();
        });
        Schema::table('monster_races', function (Blueprint $table) {
            $table->foreign('id_super_race')->references('id')->on('monster_races')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monster_races');
    }
};
