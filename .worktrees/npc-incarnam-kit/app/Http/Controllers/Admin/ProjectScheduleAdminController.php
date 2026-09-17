<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProjectScheduleTaskRequest;
use App\Models\ProjectScheduleTask;
use App\Support\ProjectSchedule\ProjectScheduleCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Planification Laravel : désactivation et expression cron depuis l’UI (super_admin interactif uniquement).
 */
class ProjectScheduleAdminController extends Controller
{
    public function index(): Response
    {
        $handlers = ProjectScheduleCatalog::handlers();

        $tasks = ProjectScheduleTask::query()->orderBy('task_key')->get()->map(
            function (ProjectScheduleTask $t) use ($handlers): array {
                $definition = $handlers[$t->task_key] ?? null;
                $admin_route = is_array($definition) ? ($definition['admin_route'] ?? null) : null;
                $admin_href = is_string($admin_route) && Route::has($admin_route)
                    ? route($admin_route)
                    : null;

                return [
                    'id' => $t->id,
                    'task_key' => $t->task_key,
                    'label' => is_array($definition) ? ($definition['label'] ?? $t->task_key) : $t->task_key,
                    'command' => is_array($definition) ? ProjectScheduleCatalog::commandLine($definition) : '',
                    'admin_href' => $admin_href,
                    'admin_label' => is_array($definition) ? ($definition['admin_label'] ?? null) : null,
                    'enabled' => $t->enabled,
                    'cron_expression' => $t->cron_expression,
                    'without_overlapping' => $t->without_overlapping,
                ];
            }
        )->values()->all();

        return Inertia::render('Admin/project-schedule/Index', [
            'tasks' => $tasks,
            'schedulerHint' => 'Crontab serveur unique : une ligne avec `schedule:run` chaque minute (voir documentation).',
        ]);
    }

    public function update(
        UpdateProjectScheduleTaskRequest $request,
        ProjectScheduleTask $project_schedule_task,
    ): RedirectResponse {
        $project_schedule_task->update($request->validated());

        return redirect()->back()->with('success', 'Planning mis à jour pour « '.$project_schedule_task->task_key.' ».');
    }
}
