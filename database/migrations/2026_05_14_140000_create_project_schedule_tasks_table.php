<?php

declare(strict_types=1);

use App\Support\ProjectSchedule\ProjectScheduleCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_schedule_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_key', 80)->unique();
            $table->boolean('enabled')->default(true);
            $table->string('cron_expression', 120);
            $table->boolean('without_overlapping')->default(true);
            $table->timestamps();
        });

        foreach (ProjectScheduleCatalog::initialSeedRows() as $row) {
            DB::table('project_schedule_tasks')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_schedule_tasks');
    }
};
