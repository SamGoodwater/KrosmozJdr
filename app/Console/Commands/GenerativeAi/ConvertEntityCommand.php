<?php

declare(strict_types=1);

namespace App\Console\Commands\GenerativeAi;

use App\Console\ArtisanExitCode;
use App\Services\GenerativeAi\ConversionPipeline;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\FewShotExamplePool;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

/**
 * Conversion IA générique (spell, encounter, npc, item, consumable) → état auto.
 *
 * @example php artisan ia:convert spell --id=12 --brief="effet lisible à table"
 */
final class ConvertEntityCommand extends Command
{
    protected $signature = 'ia:convert
        {type : spell|encounter|npc|item|consumable (alias : monster, sorts…)}
        {--id= : Id local de la fiche}
        {--official-id= : official_id de la fiche}
        {--brief= : Brief MJ}
        {--user= : Id utilisateur admin (gate generate)}
        {--force : Ignorer les gardes de fiche jouable côté métier}';

    protected $description = 'Conversion IA d’une fiche (sort, rencontre, PNJ, objet, conso) → état auto';

    public function handle(ConversionPipeline $pipeline, SpecializationRegistry $registry): int
    {
        $typeRaw = $this->argument('type');
        $type = is_string($typeRaw) ? strtolower(trim($typeRaw)) : '';
        if ($type === '') {
            $this->error('Type requis (spell, encounter, npc, item, consumable).');

            return ArtisanExitCode::FAILURE;
        }

        try {
            $spec = $registry->forEntityType($type);
        } catch (\InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $entity = $this->resolveEntity($spec->entityType());
        if ($entity === null) {
            $this->error('Fiche introuvable (--id ou --official-id).');

            return ArtisanExitCode::FAILURE;
        }

        $userRaw = $this->option('user');
        $userId = is_numeric($userRaw) ? (int) $userRaw : null;
        $briefRaw = $this->option('brief');
        $brief = is_string($briefRaw) && trim($briefRaw) !== '' ? trim($briefRaw) : null;

        try {
            $result = $pipeline->run(new ConversionRequest(
                action: $spec->key(),
                entityType: $spec->entityType(),
                entityId: (int) $entity->getKey(),
                brief: $brief,
                force: (bool) $this->option('force'),
                userId: $userId,
            ));
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->info(sprintf(
            '%s #%d en auto. Liés : %s. Tokens %d/%d (%s). Run #%d.',
            $spec->key(),
            $result['entity_id'],
            $result['related_ids'] === [] ? '—' : implode(',', $result['related_ids']),
            $result['input_tokens'],
            $result['output_tokens'],
            $result['model'],
            $result['run_id'],
        ));

        return ArtisanExitCode::SUCCESS;
    }

    private function resolveEntity(string $entityType): ?Model
    {
        $modelClass = FewShotExamplePool::ENTITY_MODELS[$entityType] ?? null;
        if ($modelClass === null) {
            return null;
        }

        $idRaw = $this->option('id');
        if (is_numeric($idRaw) && (int) $idRaw > 0) {
            return $modelClass::query()->find((int) $idRaw);
        }

        $official = $this->option('official-id');
        if (! is_string($official) || trim($official) === '') {
            return null;
        }

        $table = (new $modelClass)->getTable();
        $schema = $modelClass::query()->getConnection()->getSchemaBuilder();
        if (! $schema->hasColumn($table, 'official_id')) {
            return null;
        }

        return $modelClass::query()->where('official_id', trim($official))->first();
    }
}
