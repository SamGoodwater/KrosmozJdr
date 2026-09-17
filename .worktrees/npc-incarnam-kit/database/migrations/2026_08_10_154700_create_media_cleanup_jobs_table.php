<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_cleanup_jobs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('status', 32)->index();
            $table->string('mode', 16)->default('dry_run');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('payload')->nullable();
            $table->json('summary')->nullable();
            $table->unsignedInteger('progress_done')->default(0);
            $table->unsignedInteger('progress_total')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_cleanup_jobs');
    }
};
