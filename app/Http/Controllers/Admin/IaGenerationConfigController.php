<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateIaGenerationConfigRequest;
use App\Models\Characteristic;
use App\Models\Entity\Item;
use App\Models\IaGenerationSetting;
use App\Services\GenerativeAi\AnthropicUsageService;
use App\Services\GenerativeAi\CostEstimator;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Services\GenerativeAi\GenerationConfigStore;
use App\Services\Seeder\Item\ItemSeederFileRepository;
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
        'consumable' => 'Consommables',
    ];

    /** @var array<string, string> */
    private const CHARACTERISTIC_GROUPS = [
        'item' => 'object',
        'spell' => 'spell',
        'monster' => 'creature',
        'npc' => 'creature',
        'consumable' => 'object',
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
            'items_seeder' => $this->itemsSeederState(),
            'usage' => app(AnthropicUsageService::class)->snapshot(),
            'estimates' => app(CostEstimator::class)->all(),
            'has_api_key' => filled(config('services.anthropic.api_key')),
        ]);
    }

    /**
     * État de l'aller-retour étalons d'équipement (base ↔ fichiers du dépôt).
     *
     * @return array{relative_root: string, file_count: int, playable_count: int, can_export: bool, can_import: bool, allowed: bool}
     */
    private function itemsSeederState(): array
    {
        return [
            'relative_root' => ItemSeederFileRepository::RELATIVE_ROOT,
            'file_count' => count(app(ItemSeederFileRepository::class)->paths()),
            'playable_count' => Item::query()->where('state', Item::STATE_PLAYABLE)->count(),
            'can_export' => app()->environment(['local', 'testing']),
            'can_import' => ! app()->environment('production'),
            'allowed' => request()->user()?->isInteractiveSuperAdmin() === true,
        ];
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
