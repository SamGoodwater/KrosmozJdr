<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->string('resolution_mode', 32)
                ->default('attack_roll')
                ->after('powerful')
                ->index();
            $table->string('attack_characteristic_key', 64)->nullable()->after('resolution_mode');
            $table->string('save_characteristic_key', 64)->nullable()->after('attack_characteristic_key');
            $table->string('save_dc_formula', 255)->nullable()->after('save_characteristic_key');
            $table->text('save_success_note')->nullable()->after('save_dc_formula');
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->dropColumn([
                'resolution_mode',
                'attack_characteristic_key',
                'save_characteristic_key',
                'save_dc_formula',
                'save_success_note',
            ]);
        });
    }
};
