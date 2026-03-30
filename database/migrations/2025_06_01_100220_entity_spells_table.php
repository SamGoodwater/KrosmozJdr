<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spells', function (Blueprint $table) {
            $table->id();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('name');
            $table->string('description');
            $table->string('effect')->nullable();
            $table->string('level')->default('1');
            $table->string('po_min', 64)->default('1');
            $table->string('po_max', 64)->default('1');
            $table->boolean('po_editable')->default(true);
            $table->string('pa')->default('3');
            $table->string('cast_per_turn')->default('1');
            $table->string('cast_per_target')->default('0');
            $table->boolean('sight_line')->default(true);
            $table->string('number_between_two_cast')->default('0');
            $table->integer('element')->default(0);
            $table->integer('category')->default(0);
            $table->boolean('is_magic')->default(true);
            $table->integer('powerful')->default(0);
            $table->string('resolution_mode', 32)->default('attack_roll')->index();
            $table->string('attack_characteristic_key', 64)->nullable();
            $table->string('save_characteristic_key', 64)->nullable();
            $table->string('save_dc_formula', 255)->nullable();
            $table->text('save_success_note')->nullable();
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->string('image')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spells');
    }
};
