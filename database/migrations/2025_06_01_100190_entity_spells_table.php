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
            $table->string('name');
            $table->string('description');
            $table->string('effect')->nullable();
            $table->integer('area')->default(0);
            $table->string('level')->default('1');
            $table->string('po')->default('1');
            $table->boolean('po_editable')->default(true);
            $table->string('pa')->default('3');
            $table->string('cast_per_turn')->default('1');
            $table->string('cast_per_target')->default('0');
            $table->boolean('sight_line')->default(true);
            $table->string('number_between_two_cast')->default('0');
            $table->boolean('number_between_two_cast_editable')->default(true);
            $table->integer('element')->default(0);
            $table->integer('category')->default(0);
            $table->boolean('is_magic')->default(true);
            $table->integer('powerful')->default(0);
            $table->tinyInteger('usable')->default(0);
            $table->string('is_visible')->default('guest');
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spells');
    }
};
