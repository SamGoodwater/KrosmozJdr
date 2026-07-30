<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('domain', 40)->default('entity')->index();
            $table->string('action', 40)->index();
            $table->string('subject_type')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->string('subject_label')->nullable();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('success')->index();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['domain', 'action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
