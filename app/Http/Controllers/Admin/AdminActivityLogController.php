<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $action = trim((string) $request->query('action', ''));
        $domain = trim((string) $request->query('domain', ''));
        $status = trim((string) $request->query('status', ''));
        $actorId = $request->integer('actor_id');

        $logs = AdminActivityLog::query()
            ->with('actor:id,name')
            ->when($action !== '', fn ($query) => $query->where('action', $action))
            ->when($domain !== '', fn ($query) => $query->where('domain', $domain))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($actorId > 0, fn ($query) => $query->where('actor_id', $actorId))
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (AdminActivityLog $log): array => [
                'id' => $log->id,
                'domain' => $log->domain,
                'action' => $log->action,
                'subject_type' => class_basename($log->subject_type),
                'subject_id' => $log->subject_id,
                'subject_label' => $log->subject_label,
                'actor_name' => $log->actor?->name,
                'status' => $log->status,
                'created_at' => $log->created_at?->format('d/m/Y H:i'),
            ])
            ->all();

        return Inertia::render('Admin/activity-log/Index', [
            'logs' => $logs,
            'trash' => $this->trashRows(),
            'filters' => [
                'action' => $action,
                'domain' => $domain,
                'status' => $status,
                'actor_id' => $actorId ?: '',
            ],
            'filterOptions' => $this->filterOptions(),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function trashRows(): array
    {
        $rows = [];
        foreach (EntityModelRegistry::modelMap() as $entityType => $class) {
            if (! in_array(SoftDeletes::class, class_uses_recursive($class), true)) {
                continue;
            }

            $query = $class::query()
                ->onlyTrashed()
                ->latest('deleted_at')
                ->limit(30);

            // Monsters / NPCs : libellé via la créature liée.
            if (method_exists($class, 'creature')) {
                $query->with('creature:id,name');
            }

            $items = $query->get();

            foreach ($items as $item) {
                $label = $item->name
                    ?? $item->title
                    ?? $item->creature?->name
                    ?? '#'.$item->getKey();

                $rows[] = [
                    'entity_type' => $entityType,
                    'id' => $item->getKey(),
                    'label' => $label,
                    'deleted_at' => $item->deleted_at?->format('d/m/Y H:i'),
                ];
            }
        }

        usort($rows, fn (array $a, array $b): int => strcmp((string) ($b['deleted_at'] ?? ''), (string) ($a['deleted_at'] ?? '')));

        return array_slice($rows, 0, 100);
    }

    /**
     * @return array{domains: list<string>, actions: list<string>, statuses: list<string>, actors: list<array{id: int, name: string}>}
     */
    private function filterOptions(): array
    {
        return [
            'domains' => AdminActivityLog::query()->select('domain')->distinct()->orderBy('domain')->pluck('domain')->filter()->values()->all(),
            'actions' => AdminActivityLog::query()->select('action')->distinct()->orderBy('action')->pluck('action')->filter()->values()->all(),
            'statuses' => AdminActivityLog::query()->select('status')->distinct()->orderBy('status')->pluck('status')->filter()->values()->all(),
            'actors' => AdminActivityLog::query()
                ->with('actor:id,name')
                ->whereNotNull('actor_id')
                ->select('actor_id')
                ->distinct()
                ->orderBy('actor_id')
                ->get()
                ->map(fn (AdminActivityLog $log): ?array => $log->actor ? [
                    'id' => (int) $log->actor->id,
                    'name' => (string) $log->actor->name,
                ] : null)
                ->filter()
                ->values()
                ->all(),
        ];
    }
}
