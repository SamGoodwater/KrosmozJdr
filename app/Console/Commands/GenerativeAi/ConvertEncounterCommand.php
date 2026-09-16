<?php

declare(strict_types=1);

namespace App\Console\Commands\GenerativeAi;

use App\Console\ArtisanExitCode;
use App\Models\Entity\Monster;
use App\Services\GenerativeAi\ConversionPipeline;
use App\Services\GenerativeAi\ConversionRequest;
use Illuminate\Console\Command;

/**
 * Convertit une rencontre (monstre + 2–3 sorts-créature) via le pipeline IA.
 *
 * @example php artisan ia:convert-encounter --id=12 --brief="chef Bouftou niveau 10"
 */
final class ConvertEncounterCommand extends Command
{
    protected $signature = 'ia:convert-encounter
        {--id= : Id local du monstre}
        {--official-id= : official_id du monstre (ex. jdr:bestiary:piou-vert)}
        {--brief= : Brief MJ}
        {--user= : Id utilisateur admin (gate generate)}
        {--force : Ignorer les gardes de fiche jouable côté métier}';

    protected $description = 'Conversion IA d’une rencontre (monstre + 2–3 sorts-créature) → état auto';

    public function handle(ConversionPipeline $pipeline): int
    {
        $monster = $this->resolveMonster();
        if ($monster === null) {
            $this->error('Monstre introuvable (--id ou --official-id).');

            return ArtisanExitCode::FAILURE;
        }

        $userRaw = $this->option('user');
        $userId = is_numeric($userRaw) ? (int) $userRaw : null;
        $briefRaw = $this->option('brief');
        $brief = is_string($briefRaw) && trim($briefRaw) !== '' ? trim($briefRaw) : null;

        try {
            $result = $pipeline->run(new ConversionRequest(
                action: 'encounter',
                entityType: 'monster',
                entityId: (int) $monster->id,
                brief: $brief,
                force: (bool) $this->option('force'),
                userId: $userId,
            ));
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->info(sprintf(
            'Rencontre #%d en auto. Sorts : %s. Tokens %d/%d (%s). Run #%d.',
            $result['entity_id'],
            $result['related_ids'] === [] ? '—' : implode(',', $result['related_ids']),
            $result['input_tokens'],
            $result['output_tokens'],
            $result['model'],
            $result['run_id'],
        ));

        return ArtisanExitCode::SUCCESS;
    }

    private function resolveMonster(): ?Monster
    {
        $idRaw = $this->option('id');
        if (is_numeric($idRaw) && (int) $idRaw > 0) {
            return Monster::query()->find((int) $idRaw);
        }
        $official = $this->option('official-id');
        if (is_string($official) && trim($official) !== '') {
            return Monster::query()->where('official_id', trim($official))->first();
        }

        return null;
    }
}
