<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_threads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('status', 32)->default('open')->index();
            $table->string('url', 500)->nullable();
            $table->string('subject_preview', 160);
            $table->timestamp('last_message_at')->nullable()->index();
            $table->unsignedInteger('user_unread_count')->default(0);
            $table->unsignedInteger('staff_unread_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_threads');
    }
};
