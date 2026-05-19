<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Entity\Breed;
use App\Models\Entity\Campaign;
use App\Models\Entity\Capability;
use App\Models\Entity\Condition;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Shop;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Statistiques pour les tableaux de bord administration et gestion du contenu.
 */
class AdminOverviewStatsService
{
    /** @var array<string, class-string<Model>> */
    private const ENTITY_MODELS = [
        'breeds' => Breed::class,
        'spells' => Spell::class,
        'capabilities' => Capability::class,
        'monsters' => Monster::class,
        'items' => Item::class,
        'consumables' => Consumable::class,
        'resources' => Resource::class,
        'panoplies' => Panoply::class,
        'conditions' => Condition::class,
        'creature-traits' => CreatureTrait::class,
        'specializations' => Specialization::class,
        'campaigns' => Campaign::class,
        'scenarios' => Scenario::class,
        'shops' => Shop::class,
        'creatures' => Creature::class,
        'npcs' => Npc::class,
    ];

    private const STATE_LABELS = [
        'raw' => 'Brut',
        'draft' => 'Brouillon',
        'playable' => 'Jouable',
        'archived' => 'Archivé',
    ];

    /**
     * @return array{
     *   entities: list<array{key: string, label: string, total: int, byState: array<string, int>}>,
     *   cms: array{pages: int, sections: int}
     * }
     */
    public function contentOverview(): array
    {
        $entities = [];
        foreach (self::ENTITY_MODELS as $key => $modelClass) {
            $entities[] = $this->entityBreakdown($key, $this->labelForKey($key), $modelClass);
        }

        return [
            'entities' => $entities,
            'cms' => [
                'pages' => Page::query()->count(),
                'sections' => Section::query()->count(),
            ],
        ];
    }

    /**
     * @return array{
     *   usersByRole: list<array{role: int, label: string, count: int}>,
     *   userGrowth: list<array{month: string, count: int}>,
     *   totals: array{users: int}
     * }
     */
    public function adminRecap(): array
    {
        $roleCounts = User::query()
            ->select('role', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('role')
            ->pluck('aggregate', 'role');

        $roleLabels = [
            User::ROLE_GUEST => 'Invité',
            User::ROLE_USER => 'Utilisateur',
            User::ROLE_PLAYER => 'Joueur',
            User::ROLE_GAME_MASTER => 'Meneur de jeu',
            User::ROLE_ADMIN => 'Administrateur',
            User::ROLE_SUPER_ADMIN => 'Super administrateur',
        ];
        $usersByRole = [];
        foreach ($roleLabels as $role => $label) {
            $usersByRole[] = [
                'role' => (int) $role,
                'label' => $label,
                'count' => (int) ($roleCounts[$role] ?? 0),
            ];
        }

        $since = now()->subMonths(11)->startOfMonth();
        $rows = User::query()
            ->where('created_at', '>=', $since)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as aggregate')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('aggregate', 'month');

        $userGrowth = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $since->copy()->addMonths($i)->format('Y-m');
            $userGrowth[] = [
                'month' => $month,
                'count' => (int) ($rows[$month] ?? 0),
            ];
        }

        return [
            'usersByRole' => $usersByRole,
            'userGrowth' => $userGrowth,
            'totals' => [
                'users' => User::query()->count(),
            ],
        ];
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @return array{key: string, label: string, total: int, byState: array<string, int>}
     */
    private function entityBreakdown(string $key, string $label, string $modelClass): array
    {
        $counts = $modelClass::query()
            ->select('state', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('state')
            ->pluck('aggregate', 'state');

        $byState = [];
        foreach (array_keys(self::STATE_LABELS) as $state) {
            $byState[$state] = (int) ($counts[$state] ?? 0);
        }

        return [
            'key' => $key,
            'label' => $label,
            'total' => array_sum($byState),
            'byState' => $byState,
        ];
    }

    private function labelForKey(string $key): string
    {
        return match ($key) {
            'breeds' => 'Classes',
            'creature-traits' => 'Traits',
            default => ucfirst(str_replace('-', ' ', $key)),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function stateLabels(): array
    {
        return self::STATE_LABELS;
    }

    /**
     * Couleurs DaisyUI / entité pour les graphiques.
     *
     * @return array<string, string>
     */
    public static function stateColors(): array
    {
        return [
            'raw' => '#94a3b8',
            'draft' => '#fbbf24',
            'playable' => '#34d399',
            'archived' => '#f87171',
        ];
    }
}
