<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligne la pivot avec le modèle (`withPivot('level')`) lorsque la table
 * existait déjà sans cette colonne (anciennes bases / migration partielle).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('creature_trait_specialization')) {
            return;
        }

        if (Schema::hasColumn('creature_trait_specialization', 'level')) {
            return;
        }

        Schema::table('creature_trait_specialization', function (Blueprint $table): void {
            $table->unsignedSmallInteger('level')->default(1)->after('creature_trait_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('creature_trait_specialization')) {
            return;
        }

        if (! Schema::hasColumn('creature_trait_specialization', 'level')) {
            return;
        }

        Schema::table('creature_trait_specialization', function (Blueprint $table): void {
            $table->dropColumn('level');
        });
    }
};
