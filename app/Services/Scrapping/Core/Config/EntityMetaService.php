<?php

namespace App\Services\Scrapping\Core\Config;

/**
 * Métadonnées des types d'entités scrappables (types autorisés, maxId).
 *
 * S'appuie sur la config (entities/*.json → meta.maxId) et les alias (collect_aliases.json).
 * Un seul fallback maxId lorsque la config ne fournit pas meta.maxId.
 */
final class EntityMetaService
{
    /** Limite maxId par défaut lorsque la config n'a pas meta.maxId (ex. API non interrogée). */
    private const DEFAULT_MAX_ID = 50_000;

    /** Noms d'entités de config considérés comme importables (hors item-type, monster-race, etc.). */
    private const IMPORTABLE_ENTITIES = ['breed', 'item', 'monster', 'panoply', 'spell'];

    public function __construct(
        private ConfigLoader $configLoader,
        private CollectAliasResolver $aliasResolver
    ) {}

    /**
     * Types acceptés pour import / recherche (entités config + alias class, resource, consumable, equipment).
     *
     * @return list<string>
     */
    public function allowedTypes(): array
    {
        $entities = $this->configLoader->listEntities('dofusdb');
        $base = array_values(array_intersect($entities, self::IMPORTABLE_ENTITIES));
        $allowed = $base;
        if (in_array('breed', $base, true)) {
            $allowed[] = 'class';
        }
        if (in_array('item', $base, true)) {
            $allowed[] = 'resource';
            $allowed[] = 'consumable';
            $allowed[] = 'equipment';
        }
        sort($allowed);

        return $allowed;
    }

    /**
     * Retourne la limite maxId pour un type : config (meta.maxId) en priorité, fallback sinon.
     */
    public function getMaxIdForType(string $type): int
    {
        $resolved = $this->resolveType($type);
        try {
            $cfg = $this->configLoader->loadEntity($resolved['source'], $resolved['entity']);
            $maxId = (int) (($cfg['meta']['maxId'] ?? 0) ?: 0);
            if ($maxId > 0) {
                return $maxId;
            }
        } catch (\Throwable) {
            // config absente ou invalide
        }

        return self::DEFAULT_MAX_ID;
    }

    /**
     * Résout un type (ex. class, resource) vers source + entity de config.
     *
     * @return array{source: string, entity: string}
     */
    public function resolveType(string $type): array
    {
        $cfg = $this->aliasResolver->resolve($type);
        if ($cfg !== null) {
            return [
                'source' => (string) ($cfg['source'] ?? 'dofusdb'),
                'entity' => (string) ($cfg['entity'] ?? $type),
            ];
        }

        return ['source' => 'dofusdb', 'entity' => $type === 'class' ? 'breed' : $type];
    }

    /**
     * Indique si un type est autorisé pour l'import / la recherche.
     */
    public function isAllowedType(string $type): bool
    {
        return in_array(strtolower(trim($type)), $this->allowedTypes(), true);
    }
}
