<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_exports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('data_subject_request_id')->nullable()->constrained('data_subject_requests')->nullOnDelete();
            $table->string('status', 32)->default('pending'); // pending|processing|ready|failed|expired
            $table->string('path');
            $table->string('checksum', 64)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'expires_at'], 'privacy_exports_user_status_expires_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_exports');
    }
};

