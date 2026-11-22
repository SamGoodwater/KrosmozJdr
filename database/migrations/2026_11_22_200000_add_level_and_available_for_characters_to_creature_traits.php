<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creature_traits', function (Blueprint $table): void {
            $table->unsignedTinyInteger('level')->nullable()->after('description');
            $table->boolean('available_for_characters')->default(false)->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('creature_traits', function (Blueprint $table): void {
            $table->dropColumn(['level', 'available_for_characters']);
        });
    }
};
