<?php

declare(strict_types=1);

namespace App\Services\Seeder\Item;

use App\Models\Entity\Item;
use Illuminate\Support\Facades\File;

/**
 * Réécrit des descriptions d’équipements brouillon.
 *
 * Le catalogue couvre les fiches déjà relues. Les trous de grille, qui n’avaient
 * qu’une note interne, reçoivent une phrase joueur. `auto_update` passe à faux.
 *
 * @example $count = app(ApplyDraftItemDescriptions::class)->apply();
 */
final class ApplyDraftItemDescriptions
{
    public const CATALOG_PATH = 'database/seeders/data/entities/items/draft-descriptions.json';

    public const GRID_SOURCE = 'Objet de grille JDR (trou). Bonus techniques, à relire avant publication. Pas de source Dofus.';

    public const GRID_DESCRIPTION = 'La description n’est pas encore écrite. Les bonus sont déjà là : relis-les avant de t’en servir à la table.';

    /**
     * @return int Nombre de fiches brouillon mises à jour
     */
    public function apply(): int
    {
        $updated = Item::query()
            ->where('state', Item::STATE_DRAFT)
            ->where('description', self::GRID_SOURCE)
            ->update([
                'description' => self::GRID_DESCRIPTION,
                'auto_update' => false,
            ]);

        foreach ($this->descriptions() as $dofusdbId => $description) {
            $updated += Item::query()
                ->where('state', Item::STATE_DRAFT)
                ->where('dofusdb_id', (string) $dofusdbId)
                ->update([
                    'description' => $description,
                    'auto_update' => false,
                ]);
        }

        return $updated;
    }

    /**
     * @return array<string, string> Identifiant DofusDB => description
     *
     * @example app(ApplyDraftItemDescriptions::class)->descriptions()['77']
     */
    public function descriptions(): array
    {
        $path = base_path(self::CATALOG_PATH);
        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        $descriptions = [];
        foreach ($decoded as $dofusdbId => $description) {
            if (! is_string($description)) {
                continue;
            }
            $trimmed = trim($description);
            if ($trimmed === '') {
                continue;
            }
            $descriptions[(string) $dofusdbId] = $trimmed;
        }

        return $descriptions;
    }
}
