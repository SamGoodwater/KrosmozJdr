<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('feedback_thread_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_role', 16);
            $table->text('body');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->timestamps();

            $table->index(['feedback_thread_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_messages');
    }
};
