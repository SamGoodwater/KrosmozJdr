<?php

declare(strict_types=1);

namespace App\Console\Commands\GenerativeAi;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Breed;
use App\Services\GenerativeAi\NpcKitCatalog;
use App\Support\Npc\NpcRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Liste compacte d’équipements / sorts / gabarit pour un futur assembleur PNJ (sans LLM).
 *
 * @example php artisan ia:npc-kit-catalog --level=4 --voie=terre --breed=Iop --role=guard
 */
final class NpcKitCatalogCommand extends Command
{
    protected $signature = 'ia:npc-kit-catalog
        {--level=4 : Niveau du PNJ (1–20)}
        {--voie= : Voie d’équipement (terre, feu, eau, air)}
        {--breed= : Nom de classe (ex. Iop)}
        {--role=other : Rôle PNJ (social, merchant, guard, ally, enemy, other)}
        {--json= : Écrit le payload JSON (chemin fichier)}';

    protected $description = 'Pré-filtre compact PNJ (équipement, classes/spés, sorts par classe, gabarit 5.1.2) — sans LLM';

    public function handle(NpcKitCatalog $catalog): int
    {
        $levelRaw = $this->option('level');
        $level = is_numeric($levelRaw) ? max(1, min(20, (int) $levelRaw)) : 4;

        $voieRaw = $this->option('voie');
        $voie = is_string($voieRaw) && trim($voieRaw) !== '' ? strtolower(trim($voieRaw)) : null;

        $roleRaw = $this->option('role');
        $role = is_string($roleRaw) && in_array($roleRaw, NpcRole::values(), true)
            ? $roleRaw
            : NpcRole::OTHER;

        $breedId = null;
        $breedName = $this->option('breed');
        if (is_string($breedName) && trim($breedName) !== '') {
            $breedId = Breed::query()->where('name', trim($breedName))->value('id');
            if ($breedId === null) {
                $this->error('Classe introuvable : '.$breedName);

                return ArtisanExitCode::FAILURE;
            }
            $breedId = (int) $breedId;
        }

        $payload = $catalog->assemble($level, $voie, $breedId, $role);

        $this->info(sprintf(
            'Niveau %d · rôle %s · voie %s · classe %s',
            $level,
            NpcRole::label($role) ?: $role,
            $voie ?? 'toutes',
            is_string($breedName) && $breedName !== '' ? $breedName : '—'
        ));
        $spellsByBreed = is_array($payload['spells_by_breed'] ?? null) ? $payload['spells_by_breed'] : [];
        $spellByBreedCount = 0;
        foreach ($spellsByBreed as $rows) {
            $spellByBreedCount += is_array($rows) ? count($rows) : 0;
        }
        $this->info(sprintf(
            'Gabarits : %s PV, CA %s, %s, bande %s. Objets : %d. Sorts (classe) : %d. Sorts par classe : %d. Classes : %d. Spés : %d. Étalons : %d.',
            $payload['gabarit']['life'] ?? '—',
            $payload['gabarit']['ca'] ?? '—',
            $payload['gabarit']['damage_dice'] ?? '—',
            $payload['gabarit']['band'] ?? '—',
            count($payload['items']),
            count($payload['spells']),
            $spellByBreedCount,
            count($payload['breeds'] ?? []),
            count($payload['specializations'] ?? []),
            count($payload['example_ids']),
        ));

        $itemRows = [];
        foreach (array_slice($payload['items'], 0, 20) as $item) {
            $itemRows[] = [
                $item['slot'],
                $item['name'],
                (string) $item['level'],
                '#'.$item['id'],
            ];
        }
        if ($itemRows !== []) {
            $this->table(['Slot', 'Objet', 'Niv', 'Id'], $itemRows);
        }

        $jsonPath = $this->option('json');
        if (is_string($jsonPath) && $jsonPath !== '') {
            $dir = dirname($jsonPath);
            if ($dir !== '.' && $dir !== '' && ! is_dir($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            File::put($jsonPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            $this->info('JSON : '.$jsonPath);
        }

        return ArtisanExitCode::SUCCESS;
    }
}
