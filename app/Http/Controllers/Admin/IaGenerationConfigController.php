<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateIaGenerationConfigRequest;
use App\Models\Characteristic;
use App\Models\IaGenerationSetting;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Services\GenerativeAi\GenerationConfigStore;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IaGenerationConfigController extends Controller
{
    /** @var array<string, string> */
    private const ENTITY_LABELS = [
        'item' => 'Objets',
        'spell' => 'Sorts',
        'monster' => 'Monstres',
        'npc' => 'PNJ',
    ];

    /** @var array<string, string> */
    private const CHARACTERISTIC_GROUPS = [
        'item' => 'object',
        'spell' => 'spell',
        'monster' => 'creature',
        'npc' => 'creature',
    ];

    public function edit(GenerationConfigStore $store): Response
    {
        $row = $store->isStored()
            ? IaGenerationSetting::query()->orderBy('id')->first()
            : null;

        return Inertia::render('Admin/Content/IaGeneration/Index', [
            'config' => $store->currentPayload(),
            'is_stored' => $store->isStored(),
            'updated_at' => $row?->updated_at?->toIso8601String(),
            'entity_labels' => self::ENTITY_LABELS,
            'characteristic_options' => $this->characteristicOptions(),
        ]);
    }

    public function update(UpdateIaGenerationConfigRequest $request, GenerationConfigStore $store): RedirectResponse
    {
        $store->save($request->payload(), $request->user()?->id);

        return redirect()
            ->route('admin.content.ia-generation.edit')
            ->with('success', 'Réglages IA enregistrés.');
    }

    public function destroy(GenerationConfigStore $store): RedirectResponse
    {
        abort_unless(request()->user()?->isAdmin() === true, 403);

        $store->reset();

        return redirect()
            ->route('admin.content.ia-generation.edit')
            ->with('success', 'Réglages IA réinitialisés (fichier du dépôt).');
    }

    /**
     * @return array<string, list<array{key: string, name: string}>>
     */
    private function characteristicOptions(): array
    {
        $rows = Characteristic::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['key', 'name', 'group']);

        $out = [];
        foreach (GenerationConfigLoader::ENTITY_TYPES as $entity) {
            $group = self::CHARACTERISTIC_GROUPS[$entity];
            $out[$entity] = $rows
                ->where('group', $group)
                ->map(static fn (Characteristic $row): array => [
                    'key' => $row->key,
                    'name' => $row->name !== '' ? $row->name : $row->key,
                ])
                ->values()
                ->all();
        }

        return $out;
    }
}
