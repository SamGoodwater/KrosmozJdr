<?php

declare(strict_types=1);

namespace App\Support\Cms;

use App\Models\Entity\Campaign;
use App\Models\Entity\Capability;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Scenario;
use App\Models\Entity\Spell;
use App\Support\KrefEntityRegistry;
use Illuminate\Database\Eloquent\Model;

/**
 * Convertit les shortcodes {@code [[kref:type:cible|libellé]]} en spans `.kref` pour TipTap / lecture CMS.
 *
 * Syntaxe :
 * - {@code [[kref:characteristic:action_points_creature|Points d'action]]}
 * - {@code [[kref:page:regles-3-2-combat|Combat]]}
 * - {@code [[kref:pageSection:regles-3-2-combat@regle-3-2-2-tour-de-jeu-et-actions|Tour de jeu]]}
 * - {@code [[kref:entity:spells:42|Boule de feu]]} ou {@code [[kref:entity:spells:Boule de feu|Boule de feu]]} (résolution par nom)
 */
final class KrefShortcodeReplacer
{
    /** @var array<int, string> */
    private const ALLOWED_TYPES = ['characteristic', 'entity', 'page', 'pageSection', 'page_section'];

    /** @var (callable(string, string): (int|string|null))|null */
    private $entityIdResolver;

    /**
     * @param  (callable(string, string): (int|string|null))|null  $entityIdResolver
     */
    public function __construct(?callable $entityIdResolver = null)
    {
        $this->entityIdResolver = $entityIdResolver;
    }

    public static function forEssentialPages(): self
    {
        return new self(function (string $entityType, string $nameOrId): int|string|null {
            if ($nameOrId === '' || ctype_digit($nameOrId)) {
                return ctype_digit($nameOrId) ? (int) $nameOrId : null;
            }
            if (! KrefEntityRegistry::isAllowedType($entityType)) {
                return null;
            }

            $modelClass = match ($entityType) {
                'spells' => Spell::class,
                'items' => Item::class,
                'resources' => \App\Models\Entity\Resource::class,
                'consumables' => Consumable::class,
                'monsters' => Monster::class,
                'npcs' => Npc::class,
                'campaigns' => Campaign::class,
                'scenarios' => Scenario::class,
                'panoplies' => Panoply::class,
                'capabilities' => Capability::class,
                'creatures' => Creature::class,
                default => null,
            };

            if ($modelClass === null || ! is_subclass_of($modelClass, Model::class)) {
                return null;
            }

            /** @var Model|null $model */
            $model = $modelClass::query()->where('name', $nameOrId)->orderBy('id')->first();

            return $model !== null ? (int) $model->getKey() : null;
        });
    }

    public function replace(string $content): string
    {
        if ($content === '' || ! str_contains($content, '[[kref:')) {
            return $content;
        }

        $pattern = '/\[\[kref:([a-zA-Z_]+):([^\]|]+)(?:\|([^\]]+))?\]\]/u';

        return (string) preg_replace_callback($pattern, function (array $matches): string {
            $rawType = trim((string) ($matches[1] ?? ''));
            $rawTarget = trim((string) ($matches[2] ?? ''));
            $label = trim((string) ($matches[3] ?? ''));

            if ($rawType === '' || $rawTarget === '' || ! in_array($rawType, self::ALLOWED_TYPES, true)) {
                return (string) $matches[0];
            }

            $type = $rawType === 'page_section' ? 'pageSection' : $rawType;
            $payload = $this->buildPayload($type, $rawTarget);
            if ($payload === null) {
                return (string) $matches[0];
            }

            $finalLabel = $label !== '' ? $label : $rawTarget;
            $title = $this->encodeTitle($type, $payload, $finalLabel);
            $classes = $this->isNavigable($type) ? 'kref kref--nav' : 'kref';

            return '<span class="'.$classes.'" title="'.e($title).'">'.e($finalLabel).'</span>';
        }, $content);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPayload(string $type, string $target): ?array
    {
        if ($type === 'characteristic') {
            return ['key' => trim($target)];
        }

        if ($type === 'page') {
            return ['pageSlug' => trim($target)];
        }

        if ($type === 'pageSection') {
            if (str_contains($target, '@')) {
                [$pageSlug, $sectionSlug] = array_pad(explode('@', $target, 2), 2, '');
                $pageSlug = trim((string) $pageSlug);
                $sectionSlug = trim((string) $sectionSlug);
                if ($pageSlug === '' || $sectionSlug === '') {
                    return null;
                }

                return ['pageSlug' => $pageSlug, 'sectionSlug' => $sectionSlug];
            }

            [$pageSlug, $sectionId] = array_pad(explode(':', $target, 2), 2, '');
            $pageSlug = trim((string) $pageSlug);
            $sectionId = trim((string) $sectionId);
            if ($pageSlug === '' || $sectionId === '') {
                return null;
            }

            return ['pageSlug' => $pageSlug, 'sectionId' => ctype_digit($sectionId) ? (int) $sectionId : $sectionId];
        }

        if ($type === 'entity') {
            [$entityType, $idOrName] = array_pad(explode(':', $target, 2), 2, '');
            $entityType = trim((string) $entityType);
            $idOrName = trim((string) $idOrName);
            if ($entityType === '' || $idOrName === '') {
                return null;
            }

            $resolvedId = $idOrName;
            if (! ctype_digit($idOrName) && $this->entityIdResolver !== null) {
                $resolved = ($this->entityIdResolver)($entityType, $idOrName);
                if ($resolved === null || $resolved === '') {
                    return null;
                }
                $resolvedId = $resolved;
            }

            return ['entityType' => $entityType, 'id' => is_numeric($resolvedId) ? (int) $resolvedId : $resolvedId];
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function encodeTitle(string $type, array $payload, string $label): string
    {
        $json = json_encode([
            't' => $type,
            'p' => $payload,
            'l' => trim($label),
        ], JSON_UNESCAPED_UNICODE);

        if (! is_string($json) || $json === '') {
            return '';
        }

        return rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    }

    private function isNavigable(string $type): bool
    {
        return in_array($type, ['entity', 'page', 'pageSection'], true);
    }
}
