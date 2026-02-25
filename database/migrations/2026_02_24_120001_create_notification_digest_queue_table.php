<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * File d'attente des notifications en mode digest (quotidien, hebdo, mensuel).
     */
    public function up(): void
    {
        Schema::create('notification_digest_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('notification_type', 64);
            $table->string('frequency', 16)->default('daily');
            $table->json('payload');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'notification_type', 'frequency'], 'ndq_user_type_freq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_digest_queue');
    }
};
