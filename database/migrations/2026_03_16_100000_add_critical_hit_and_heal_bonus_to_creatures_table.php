<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatures', function (Blueprint $table) {
            $table->string('critical_hit', 16)->default('0')->after('tacle');
            $table->string('heal_bonus', 16)->default('0')->after('critical_hit');
        });
    }

    public function down(): void
    {
        Schema::table('creatures', function (Blueprint $table) {
            $table->dropColumn(['critical_hit', 'heal_bonus']);
        });
    }
};
