<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('dofusdb_type_id')->nullable()->unique();
            $table->string('decision')->default('pending');
            $table->unsignedInteger('seen_count')->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->string('state')->default('draft');
            $table->tinyInteger('read_level')->default(0);
            $table->tinyInteger('write_level')->default(3);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_types');
    }
};
