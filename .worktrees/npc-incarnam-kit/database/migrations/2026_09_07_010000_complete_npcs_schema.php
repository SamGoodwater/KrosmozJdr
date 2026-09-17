<?php

declare(strict_types=1);

use App\Support\Creature\CreatureSize;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->unsignedTinyInteger('size_int')->default(CreatureSize::MOYEN)->after('age');
            $table->string('npc_role')->nullable()->after('size_int');
        });

        $rows = DB::table('npcs')->select('id', 'size')->get();
        foreach ($rows as $row) {
            DB::table('npcs')->where('id', $row->id)->update([
                'size_int' => CreatureSize::fromLegacy($row->size),
            ]);
        }

        Schema::table('npcs', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->renameColumn('size_int', 'size');
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropForeign(['specialization_id']);
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->foreign('breed_id')->references('id')->on('breeds')->nullOnDelete();
            $table->foreign('specialization_id')->references('id')->on('specializations')->nullOnDelete();
        });

        Schema::create('npc_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('npc_id')->constrained('npcs')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['npc_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npc_language');

        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropForeign(['specialization_id']);
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->foreign('breed_id')->references('id')->on('breeds')->cascadeOnDelete();
            $table->foreign('specialization_id')->references('id')->on('specializations')->cascadeOnDelete();
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->string('size_legacy')->nullable()->after('age');
        });

        $rows = DB::table('npcs')->select('id', 'size')->get();
        foreach ($rows as $row) {
            DB::table('npcs')->where('id', $row->id)->update([
                'size_legacy' => CreatureSize::label((int) $row->size),
            ]);
        }

        Schema::table('npcs', function (Blueprint $table) {
            $table->dropColumn(['size', 'npc_role']);
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->renameColumn('size_legacy', 'size');
        });
    }
};
