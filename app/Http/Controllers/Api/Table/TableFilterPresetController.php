<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Models\TableFilterPreset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD des presets de filtres de tableaux (persistés en BDD).
 */
class TableFilterPresetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'entity_type' => ['required', 'string', 'max:120'],
            'table_id' => ['sometimes', 'nullable', 'string', 'max:191'],
            'include_global' => ['sometimes', 'boolean'],
        ]);

        $entityType = (string) $data['entity_type'];
        $tableId = array_key_exists('table_id', $data) ? $data['table_id'] : null;
        $includeGlobal = (bool) ($data['include_global'] ?? true);

        $query = TableFilterPreset::query()
            ->where('user_id', $user->id)
            ->where('entity_type', $entityType);

        if ($tableId !== null && $tableId !== '') {
            $query->where(function ($q) use ($tableId, $includeGlobal) {
                $q->where('table_id', $tableId);
                if ($includeGlobal) {
                    $q->orWhereNull('table_id');
                }
            });
        }

        $presets = $query
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn (TableFilterPreset $preset) => $this->toPayload($preset))
            ->values()
            ->all();

        return response()->json(['presets' => $presets]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'entity_type' => ['required', 'string', 'max:120'],
            'table_id' => ['nullable', 'string', 'max:191'],
            'name' => ['required', 'string', 'max:120'],
            'search_text' => ['nullable', 'string'],
            'filters' => ['nullable', 'array'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $isDefault = (bool) ($data['is_default'] ?? false);
        $tableId = $data['table_id'] ?? null;

        if ($isDefault) {
            $this->clearDefaultForScope($user->id, (string) $data['entity_type'], $tableId);
        }

        $preset = TableFilterPreset::create([
            'user_id' => $user->id,
            'entity_type' => (string) $data['entity_type'],
            'table_id' => $tableId,
            'name' => (string) $data['name'],
            'search_text' => $data['search_text'] ?? null,
            'filters' => $data['filters'] ?? [],
            'limit' => $data['limit'] ?? null,
            'is_default' => $isDefault,
        ]);

        return response()->json(['preset' => $this->toPayload($preset)], 201);
    }

    public function update(Request $request, TableFilterPreset $tablePreset): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if ((int) $tablePreset->user_id !== (int) $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'entity_type' => ['sometimes', 'required', 'string', 'max:120'],
            'table_id' => ['sometimes', 'nullable', 'string', 'max:191'],
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'search_text' => ['sometimes', 'nullable', 'string'],
            'filters' => ['sometimes', 'nullable', 'array'],
            'limit' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:5000'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $nextEntityType = (string) ($data['entity_type'] ?? $tablePreset->entity_type);
        $nextTableId = array_key_exists('table_id', $data) ? $data['table_id'] : $tablePreset->table_id;
        $nextIsDefault = array_key_exists('is_default', $data)
            ? (bool) $data['is_default']
            : (bool) $tablePreset->is_default;

        if ($nextIsDefault) {
            $this->clearDefaultForScope($user->id, $nextEntityType, $nextTableId, $tablePreset->id);
        }

        $tablePreset->update([
            'entity_type' => $nextEntityType,
            'table_id' => $nextTableId,
            'name' => array_key_exists('name', $data) ? (string) $data['name'] : $tablePreset->name,
            'search_text' => array_key_exists('search_text', $data) ? $data['search_text'] : $tablePreset->search_text,
            'filters' => array_key_exists('filters', $data) ? ($data['filters'] ?? []) : $tablePreset->filters,
            'limit' => array_key_exists('limit', $data) ? $data['limit'] : $tablePreset->limit,
            'is_default' => $nextIsDefault,
        ]);

        return response()->json(['preset' => $this->toPayload($tablePreset->fresh())]);
    }

    public function destroy(Request $request, TableFilterPreset $tablePreset): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if ((int) $tablePreset->user_id !== (int) $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tablePreset->delete();

        return response()->json(['success' => true]);
    }

    private function clearDefaultForScope(int $userId, string $entityType, ?string $tableId, ?int $exceptId = null): void
    {
        $query = TableFilterPreset::query()
            ->where('user_id', $userId)
            ->where('entity_type', $entityType);

        if ($tableId === null || $tableId === '') {
            $query->whereNull('table_id');
        } else {
            $query->where('table_id', $tableId);
        }

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        $query->update(['is_default' => false]);
    }

    private function toPayload(TableFilterPreset $preset): array
    {
        return [
            'id' => (string) $preset->id,
            'entity_type' => $preset->entity_type,
            'table_id' => $preset->table_id,
            'name' => $preset->name,
            'search_text' => $preset->search_text ?? '',
            'filters' => $preset->filters ?? [],
            'limit' => $preset->limit,
            'is_default' => (bool) $preset->is_default,
            'created_at' => $preset->created_at?->toISOString(),
            'updated_at' => $preset->updated_at?->toISOString(),
        ];
    }
}

