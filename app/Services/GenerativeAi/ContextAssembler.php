<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Services\GenerativeAi\Specializations\Specialization;
use App\Services\GenerativeAi\Specializations\SpecializationRegistry;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Assembleur : superviseur + pack de type + few-shot compact + schéma writable-only.
 *
 * @example $assembled = app(ContextAssembler::class)->assemble($request);
 */
final class ContextAssembler
{
    public const DEFAULT_SUPERVISOR = "Tu convertis des fiches Dofus en contenu jouable pour le JDR Krosmoz.\n"
        ."Tu proposes, tu ne publies jamais (jamais playable).\n"
        ."Sortie JSON uniquement, clés autorisées par le schéma, rien d’autre.\n"
        ."Tu n’inventes pas d’identifiants hors listes fournies.\n"
        .'Sur une fiche sourcée Dofus, tu ne réécris pas l’identité (nom, description, type, image).';

    public function assemble(ConversionRequest $request): AssembledPrompt
    {
        $spec = app(SpecializationRegistry::class)->forAction($request->action);
        $profile = GenerationConfigLoader::default()->forEntity($spec->entityType());
        $examples = $this->fewShot($spec, $profile);

        if ($spec->entityType() === 'item') {
            $names = $profile->extra['few_shot_panoplies'] ?? [];
            if (is_array($names)) {
                $strings = [];
                foreach ($names as $name) {
                    if (is_string($name) && trim($name) !== '') {
                        $strings[] = trim($name);
                    }
                }
                app(FewShotPanoplyGuard::class)->assertPlayable($strings);
            }
        }

        $schema = $spec->jsonSchema($profile, $request);
        $supervisor = $this->supervisor();
        $user = $this->userMessage($spec, $profile, $request, $examples);

        return new AssembledPrompt(
            supervisor: $supervisor,
            userMessage: $user,
            schema: $schema,
            profile: $profile,
            examples: $examples,
        );
    }

    public function supervisor(): string
    {
        $fromConfig = GenerationConfigLoader::default()->get('supervisor_prompt');
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return self::DEFAULT_SUPERVISOR;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fewShot(Specialization $spec, EntityGenerationProfile $profile): array
    {
        $configured = $profile->exampleIds;
        $fallback = app(FewShotFallback::class)->idsFor($spec->entityType());
        $ids = app(FewShotExamplePool::class)->requireIds($spec->entityType(), $configured, $fallback);
        $count = (int) (GenerationConfigLoader::default()->get('generation.few_shot_count', 8) ?: 8);
        $ids = array_slice($ids, 0, max(1, min(30, $count)));

        $modelClass = FewShotExamplePool::ENTITY_MODELS[$spec->entityType()] ?? null;
        if ($modelClass === null) {
            throw new RuntimeException('Type few-shot inconnu : '.$spec->entityType());
        }

        $models = $modelClass::query()->whereIn('id', $ids)->get();
        $byId = [];
        foreach ($models as $model) {
            $byId[(int) $model->getKey()] = $model;
        }

        $out = [];
        foreach ($ids as $id) {
            $model = $byId[$id] ?? null;
            if ($model instanceof Model) {
                $out[] = $spec->compactExample($model);
            }
        }

        if ($out === []) {
            throw new RuntimeException('Pool few-shot vide pour '.$spec->entityType().' : aucun étalon playable.');
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $examples
     */
    private function userMessage(
        Specialization $spec,
        EntityGenerationProfile $profile,
        ConversionRequest $request,
        array $examples,
    ): string {
        $chunks = [];
        $task = $spec->taskPrompt();
        if ($task !== '') {
            $chunks[] = $task;
        }

        $chunks[] = 'Profil gel : writable_fields='.json_encode($profile->writableFields, JSON_UNESCAPED_UNICODE)
            .'; writable_characteristics='.json_encode($profile->writableCharacteristics, JSON_UNESCAPED_UNICODE)
            .'; has_dofus_source='.($profile->hasDofusSource ? 'true' : 'false').'.';

        $source = $this->sourceSnapshot($spec, $request);
        if ($source !== null) {
            $chunks[] = "Fiche source :\n".json_encode($source, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_string($request->brief) && trim($request->brief) !== '') {
            $chunks[] = 'Brief MJ : '.trim($request->brief);
        }

        $extra = $spec->extraContext($request, $profile);
        if ($extra !== []) {
            $chunks[] = "Contexte métier (listes autorisées, gabarit) :\n"
                .json_encode($extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $chunks[] = "Exemples playable (à imiter, ne pas republier) :\n"
            .json_encode($examples, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $chunks[] = 'Réponds uniquement via l’outil JSON. 1 paquet = 1 réponse.';

        return implode("\n\n", $chunks);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function sourceSnapshot(Specialization $spec, ConversionRequest $request): ?array
    {
        if ($request->entityId === null) {
            return null;
        }
        $modelClass = FewShotExamplePool::ENTITY_MODELS[$spec->entityType()] ?? null;
        if ($modelClass === null) {
            return null;
        }
        $model = $modelClass::query()->find($request->entityId);
        if (! $model instanceof Model) {
            return null;
        }

        return $spec->compactExample($model);
    }
}
