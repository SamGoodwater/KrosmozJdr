<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->boolean('cast_in_line')->default(false)->after('sight_line');
            $table->boolean('cast_in_diagonal')->default(false)->after('cast_in_line');
            $table->string('target_type', 16)->nullable()->after('cast_in_diagonal');
            $table->unsignedTinyInteger('max_stack')->default(0)->after('target_type');
            $table->unsignedTinyInteger('global_cooldown')->default(0)->after('max_stack');
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->dropColumn([
                'cast_in_line',
                'cast_in_diagonal',
                'target_type',
                'max_stack',
                'global_cooldown',
            ]);
        });
    }
};
