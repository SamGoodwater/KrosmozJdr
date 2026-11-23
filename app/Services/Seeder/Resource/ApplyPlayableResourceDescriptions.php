<?php

declare(strict_types=1);

namespace App\Services\Seeder\Resource;

use App\Models\Entity\Resource;
use Illuminate\Support\Facades\File;

/**
 * Réécrit la description des ressources jouables depuis le catalogue de ton.
 *
 * Le scrap Dofus laisse ces fiches en « vous ». Le catalogue les passe en « tu ».
 * `auto_update` passe à faux : sinon le prochain scrap remet l’ancien texte.
 *
 * @example $count = app(ApplyPlayableResourceDescriptions::class)->apply();
 */
final class ApplyPlayableResourceDescriptions
{
    public const CATALOG_PATH = 'database/seeders/data/entities/resources/playable-descriptions.json';

    /**
     * @return int Nombre de fiches jouables dont la description a été posée
     */
    public function apply(): int
    {
        $updated = 0;

        foreach ($this->descriptions() as $dofusdbId => $description) {
            $updated += Resource::query()
                ->where('state', Resource::STATE_PLAYABLE)
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
     * @example app(ApplyPlayableResourceDescriptions::class)->descriptions()['289']
     */
    public function descriptions(): array
    {
        $path = base_path(self::CATALOG_PATH);
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
