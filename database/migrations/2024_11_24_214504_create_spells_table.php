<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spells', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('uniqid', 20)->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('description');
            $table->string('effect');
            $table->string('effect_array')->nullable();
            $table->integer('area')->default(0);
            $table->integer('level')->default(1);
            $table->string('po')->default('1');
            $table->boolean('po_editable')->default(true);
            $table->string('pa')->default('4');
            $table->string('cast_per_turn')->default('1');
            $table->integer('cast_per_target')->default(0);
            $table->boolean('sight_line')->default(true);
            $table->string('number_between_two_cast')->default('0');
            $table->integer('element')->default(0);
            $table->integer('category')->default(0);
            $table->boolean('is_magic')->default(true);
            $table->integer('powerful')->default(2);
            $table->boolean('usable')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->softDeletes();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('spell_invocation', function (Blueprint $table) {
            $table->primary(['spell_id', 'mob_id']);
            $table->foreignIdFor(\App\Models\Modules\Spell::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Mob::class)->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('spell_type', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Modules\Spell::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Modules\Spelltype::class)->constrained()->cascadeOnDelete();
            $table->primary(['spell_id', 'spelltype_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spells');
        Schema::table('spells', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'created_by');
        });
        Schema::dropIfExists('spell_types');
        Schema::dropIfExists('spell_invocation');
    }
};
