<?php

namespace App\Services\Scrapping\Registry;

use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Batch touch des registries de typeId DofusDB.
 *
 * @description
 * Optimisation: au lieu d'appeler `touchDofusdbType()` dans une boucle (N writes),
 * on fait:
 * - insertOrIgnore des lignes manquantes (placeholders)
 * - update en masse seen_count + last_seen_at
 *
 * Fonctionne pour ResourceType / ItemType / ConsumableType.
 */
class TypeRegistryBatchTouchService
{
    /**
     * @param class-string<Model> $registry
     * @param array<int,int> $typeIds
     */
    public function touchMany(string $registry, array $typeIds): void
    {
        $typeIds = array_values(array_unique(array_map('intval', $typeIds)));
        $typeIds = array_values(array_filter($typeIds, fn ($v) => $v > 0));
        if (empty($typeIds)) {
            return;
        }

        /** @var Model $tmp */
        $tmp = new $registry();
        $table = $tmp->getTable();

        // Déjà présents
        $existing = DB::table($table)
            ->whereIn('dofusdb_type_id', $typeIds)
            ->pluck('dofusdb_type_id')
            ->map(fn ($v) => (int) $v)
            ->all();
        $existing = array_values(array_unique(array_map('intval', $existing)));
        $missing = array_values(array_diff($typeIds, $existing));

        $now = now();
        $systemUserId = User::getSystemUser()?->id;

        if (!empty($missing)) {
            $rows = [];
            foreach ($missing as $typeId) {
                $placeholderName = "DofusDB type #{$typeId}";

                $base = [
                    'name' => $placeholderName,
                    'dofusdb_type_id' => $typeId,
                    'decision' => 'pending',
                    'seen_count' => 0,
                    'last_seen_at' => $now,
                    'created_by' => $systemUserId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($registry === ResourceType::class) {
                    $base['state'] = ResourceType::STATE_PLAYABLE;
                    $base['read_level'] = User::ROLE_GUEST;
                    $base['write_level'] = User::ROLE_ADMIN;
                } elseif ($registry === ItemType::class || $registry === ConsumableType::class) {
                    // Schéma historique: usable/is_visible
                    $base['usable'] = 0;
                    $base['is_visible'] = 'guest';
                }

                $rows[] = $base;
            }

            // Insertion best-effort (évite les erreurs de concurrence via unique dofusdb_type_id)
            DB::table($table)->insertOrIgnore($rows);
        }

        // Touch en masse: seen_count++, last_seen_at=now
        DB::table($table)
            ->whereIn('dofusdb_type_id', $typeIds)
            ->update([
                'seen_count' => DB::raw('COALESCE(seen_count, 0) + 1'),
                'last_seen_at' => $now,
                'updated_at' => $now,
            ]);
    }
}

