<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_console_jobs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('domain', 32)->index();
            $table->string('status', 16)->index();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('progress_label', 255)->nullable();
            $table->string('command', 512);
            $table->string('page_url', 512)->nullable();
            $table->longText('output');
            $table->text('error')->nullable();
            $table->smallInteger('exit_code')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('notification_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['domain', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_console_jobs');
    }
};
