<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\MonsterRace;
use App\Models\Type\ResourceType;
use App\Models\Type\SpellType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Page unique des registres de types (équipements, ressources, consommables, races, sorts).
 */
class ContentTypeRegistryController extends Controller
{
    /** @var list<string> */
    public const KINDS = ['equipment', 'resource', 'consumable', 'race', 'spell'];

    public function index(): RedirectResponse
    {
        return redirect()->route('admin.content.types.show', ['kind' => 'equipment']);
    }

    public function show(string $kind): InertiaResponse
    {
        if (! in_array($kind, self::KINDS, true)) {
            abort(404);
        }

        $this->authorize('viewAny', $this->policyModel($kind));

        $user = request()->user();

        return Inertia::render('Admin/Content/Types/Index', [
            'kind' => $kind,
            'can' => [
                'updateAny' => $user ? $user->can('updateAny', $this->policyModel($kind)) : false,
            ],
        ]);
    }

    /**
     * @return class-string
     */
    private function policyModel(string $kind): string
    {
        return match ($kind) {
            'equipment' => ItemType::class,
            'resource' => ResourceType::class,
            'consumable' => ConsumableType::class,
            'race' => MonsterRace::class,
            'spell' => SpellType::class,
            default => ItemType::class,
        };
    }
}
