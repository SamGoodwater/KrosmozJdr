<?php

declare(strict_types=1);

namespace App\Services\Seeder\Item;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;

/**
 * Traduit un équipement entre le modèle Eloquent et le tableau versionné du seeder.
 *
 * Le fichier stocke `effect` / `bonus` sous forme d'objet JSON (éditable à la main) alors que la
 * base les garde en chaîne. Le type d'item est référencé par `item_type_dofus_id` : les identifiants
 * auto-incrémentés de `item_types` diffèrent d'un environnement à l'autre.
 *
 * @example
 * $payload = ItemSeederPayload::fromModel($item);
 * $attributes = ItemSeederPayload::toAttributes($payload);
 */
final class ItemSeederPayload
{
    public const SCHEMA_VERSION = '1';

    /**
     * Champs recopiés tels quels entre le fichier et la base.
     *
     * `image` est volontairement absent : la colonne stocke une URL absolue liée à l'hôte et à
     * l'identifiant média de l'environnement. Les visuels restent gérés par la média-library.
     *
     * @var list<string>
     */
    private const SCALAR_FIELDS = [
        'name',
        'level',
        'description',
        'recipe',
        'rarity',
        'state',
        'dofus_version',
        'read_level',
        'write_level',
        'auto_update',
        'price_calculated',
        'price_custom',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function fromModel(Item $item): array
    {
        $attributes = [];
        foreach (self::SCALAR_FIELDS as $field) {
            $attributes[$field] = $item->{$field};
        }
        $attributes['effect'] = self::decodeJsonColumn($item->effect);
        $attributes['bonus'] = self::decodeJsonColumn($item->bonus);
        $attributes['item_type_dofus_id'] = $item->itemType?->dofusdb_type_id;
        $attributes['item_type_name'] = $item->itemType?->name;

        return [
            '_schema_version' => self::SCHEMA_VERSION,
            'key' => [
                'dofusdb_id' => $item->dofusdb_id,
                'official_id' => $item->official_id,
            ],
            'item' => $attributes,
            'relations' => [
                'panoply_dofusdb_ids' => $item->panoplies
                    ->pluck('dofusdb_id')
                    ->filter(static fn ($value): bool => $value !== null && $value !== '')
                    ->values()
                    ->all(),
                'resources' => $item->resources
                    ->map(static fn ($resource): array => [
                        'dofusdb_id' => $resource->dofusdb_id,
                        'name' => $resource->name,
                        'quantity' => (int) ($resource->pivot->quantity ?? 1),
                    ])
                    ->filter(static fn (array $row): bool => $row['dofusdb_id'] !== null && $row['dofusdb_id'] !== '')
                    ->values()
                    ->all(),
            ],
        ];
    }

    /**
     * Attributs prêts pour un `updateOrCreate`. Retourne `null` si le type d'item est introuvable.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    public static function toAttributes(array $payload): ?array
    {
        $item = is_array($payload['item'] ?? null) ? $payload['item'] : [];

        $itemTypeId = self::resolveItemTypeId($item['item_type_dofus_id'] ?? null);
        if ($itemTypeId === null) {
            return null;
        }

        $attributes = ['item_type_id' => $itemTypeId];
        foreach (self::SCALAR_FIELDS as $field) {
            if (array_key_exists($field, $item)) {
                $attributes[$field] = $item[$field];
            }
        }
        foreach (['effect', 'bonus'] as $field) {
            if (array_key_exists($field, $item)) {
                $attributes[$field] = self::encodeJsonColumn($item[$field]);
            }
        }

        return $attributes;
    }

    /**
     * Couple de recherche pour l'upsert : `dofusdb_id` en priorité, sinon `official_id`.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, string>|null
     */
    public static function lookupKey(array $payload): ?array
    {
        $key = is_array($payload['key'] ?? null) ? $payload['key'] : [];
        $dofusdbId = $key['dofusdb_id'] ?? null;
        if (is_string($dofusdbId) && trim($dofusdbId) !== '') {
            return ['dofusdb_id' => trim($dofusdbId)];
        }
        if (is_int($dofusdbId)) {
            return ['dofusdb_id' => (string) $dofusdbId];
        }
        $officialId = $key['official_id'] ?? null;
        if (is_string($officialId) && trim($officialId) !== '') {
            return ['official_id' => trim($officialId)];
        }

        return null;
    }

    /**
     * Identité stable d'un item, utilisée pour nommer le fichier et détecter les doublons.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function identity(array $payload): string
    {
        $lookup = self::lookupKey($payload);
        if ($lookup === null) {
            return '';
        }

        return array_key_first($lookup).':'.reset($lookup);
    }

    private static function resolveItemTypeId(mixed $dofusTypeId): ?int
    {
        if (! is_numeric($dofusTypeId)) {
            return null;
        }

        $id = ItemType::query()
            ->where('dofusdb_type_id', (int) $dofusTypeId)
            ->value('id');

        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * @return array<string, mixed>|list<mixed>|null
     */
    private static function decodeJsonColumn(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    private static function encodeJsonColumn(mixed $value): ?string
    {
        if ($value === null || $value === [] || $value === '') {
            return null;
        }
        if (is_string($value)) {
            return $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
