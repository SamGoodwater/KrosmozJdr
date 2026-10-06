<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Zone par défaut des sorts sans degré structuré.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->string('area', 64)->nullable()->after('po_max');
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->dropColumn('area');
        });
    }
};
