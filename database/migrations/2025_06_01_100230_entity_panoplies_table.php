<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panoplies', function (Blueprint $table) {
            $table->id();
            $table->string('dofusdb_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('bonus')->nullable();
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
        Schema::table('panoplies', function (Blueprint $table) {
            $table->index('dofusdb_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panoplies');
    }
};
