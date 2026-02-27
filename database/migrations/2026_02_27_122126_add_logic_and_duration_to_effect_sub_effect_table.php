<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('effect_sub_effect', function (Blueprint $table) {
            // Durée sous forme de formule (ex: "2", "[level]/2", "3d4")
            $table->string('duration_formula', 255)->nullable()->after('dice_side');

            // Groupes logiques et opérateurs entre sous-effets (ET / OU + condition pour OU)
            $table->string('logic_group', 64)->nullable()->after('duration_formula');
            $table->string('logic_operator', 8)->nullable()->after('logic_group'); // AND / OR
            $table->string('logic_condition', 255)->nullable()->after('logic_operator');
        });
    }

    public function down(): void
    {
        Schema::table('effect_sub_effect', function (Blueprint $table) {
            $table->dropColumn(['duration_formula', 'logic_group', 'logic_operator', 'logic_condition']);
        });
    }
};
