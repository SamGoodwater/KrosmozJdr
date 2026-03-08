<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_subject_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 32); // export|erasure
            $table->string('status', 32)->default('pending'); // pending|processing|completed|failed|cancelled
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'status'], 'dsr_user_type_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_subject_requests');
    }
};

