<?php

namespace App\Services\Scrapping\Config;

use Illuminate\Support\Arr;
use App\Models\Type\ResourceType;

/**
 * Convertit des données "raw" en données "converted" via la config JSON.
 *
 * @description
 * Utilise la config d'entité (mapping + formatters) pour produire une structure
 * compatible avec KrosmozJDR (ex: creatures/monsters).
 *
 * Par défaut on peut désactiver les formatters avec effets de bord (ex: téléchargement d'image).
 */
class ConfigDrivenConverter
{
    public function __construct(
        private FormatterRegistry $registry
    ) {}

    /**
     * @param array<string,mixed> $entityConfig
     * @param array<string,mixed> $raw
     * @param array{lang?:string, apply_side_effects?:bool} $context
     * @return array<string,mixed>
     */
    public function convert(array $entityConfig, array $raw, array $context = []): array
    {
        $lang = (string) ($context['lang'] ?? 'fr');
        $applySideEffects = (bool) ($context['apply_side_effects'] ?? false);

        $out = [];

        $mapping = $entityConfig['mapping'] ?? [];
        foreach ($mapping as $map) {
            if (!is_array($map)) {
                continue;
            }

            $from = (array) ($map['from'] ?? []);
            $path = (string) ($from['path'] ?? '');
            if ($path === '') {
                continue;
            }

            $value = $this->getByPath($raw, $path);

            // Option "langAware": si c'est un objet multilingue, on garde l'objet et on laissera pickLang faire le travail.
            // (Si aucun pickLang n'est fourni, on ne force pas un choix arbitraire ici.)
            $formatters = $map['formatters'] ?? [];
            if (is_array($formatters)) {
                foreach ($formatters as $fmt) {
                    if (!is_array($fmt) || !isset($fmt['name']) || !is_string($fmt['name'])) {
                        continue;
                    }
                    $name = $fmt['name'];
                    $def = $this->registry->get($name);
                    if (!$def) {
                        continue; // déjà validé en amont, mais safe
                    }

                    $type = (string) ($def['type'] ?? 'pure');
                    if ($type === 'side_effect' && !$applySideEffects) {
                        // No-op en preview
                        continue;
                    }

                    $args = $fmt['args'] ?? [];
                    if (!is_array($args)) {
                        $args = [];
                    }
                    $args = $this->interpolateArgs($args, ['lang' => $lang]);

                    $value = $this->applyFormatter($name, $value, $args, $raw);
                }
            }

            $targets = $map['to'] ?? [];
            if (!is_array($targets)) {
                continue;
            }

            foreach ($targets as $target) {
                if (!is_array($target)) {
                    continue;
                }
                $model = $target['model'] ?? null;
                $field = $target['field'] ?? null;
                if (!is_string($model) || $model === '' || !is_string($field) || $field === '') {
                    continue;
                }

                if (!isset($out[$model]) || !is_array($out[$model])) {
                    $out[$model] = [];
                }
                $out[$model][$field] = $value;
            }
        }

        // Compat legacy (minimal) : si on a un seul modèle "spells", on remonte aussi au root.
        if (isset($out['spells']) && is_array($out['spells'])) {
            foreach ($out['spells'] as $k => $v) {
                if (!array_key_exists($k, $out)) {
                    $out[$k] = $v;
                }
            }
        }

        // Compat legacy (minimal) : si on a un modèle "items", on remonte aussi au root
        // car DataIntegrationService::integrateItem attend les clés au root (name/type/category/...).
        if (isset($out['items']) && is_array($out['items'])) {
            foreach ($out['items'] as $k => $v) {
                if (!array_key_exists($k, $out)) {
                    $out[$k] = $v;
                }
            }
        }

        return $out;
    }

    /**
     * @param array<string,mixed> $args
     * @param array<string,string> $vars
     * @return array<string,mixed>
     */
    private function interpolateArgs(array $args, array $vars): array
    {
        foreach ($args as $k => $v) {
            if (is_string($v)) {
                foreach ($vars as $var => $value) {
                    $args[$k] = str_replace('{' . $var . '}', $value, $args[$k]);
                }
            }
        }
        return $args;
    }

    /**
     * Extraction dot-path basique (support indices numériques).
     *
     * @param array<string,mixed> $data
     * @return mixed
     */
    private function getByPath(array $data, string $path)
    {
        $parts = explode('.', $path);
        $cur = $data;
        foreach ($parts as $part) {
            if (is_array($cur)) {
                if (ctype_digit($part)) {
                    $idx = (int) $part;
                    $cur = $cur[$idx] ?? null;
                    continue;
                }
                $cur = $cur[$part] ?? null;
                continue;
            }
            return null;
        }
        return $cur;
    }

    /**
     * Applique un formatter connu.
     *
     * @param array<string,mixed> $args
     * @param array<string,mixed> $raw
     * @return mixed
     */
    private function applyFormatter(string $name, $value, array $args, array $raw)
    {
        return match ($name) {
            'pickLang' => $this->fmtPickLang($value, (string) ($args['lang'] ?? 'fr'), (string) ($args['fallback'] ?? 'fr')),
            'toString' => $value === null ? '' : (string) $value,
            'toInt' => is_numeric($value) ? (int) $value : 0,
            'nullableInt' => $value === null ? null : (is_numeric($value) ? (int) $value : null),
            'clampInt' => $this->fmtClampInt($value, (int) ($args['min'] ?? 0), (int) ($args['max'] ?? 0)),
            'truncate' => $this->fmtTruncate($value, (int) ($args['max'] ?? 255)),
            'mapSizeToKrosmoz' => $this->fmtMapSize((string) $value, (string) ($args['default'] ?? 'medium')),
            'mapDofusdbItemType' => $this->fmtMapDofusdbItemType($value),
            'mapDofusdbItemCategory' => $this->fmtMapDofusdbItemCategory($value),
            // storeScrappedImage: en mode side_effect, l'implémentation réelle sera dans l'intégration.
            // Ici, on renvoie simplement l'URL brute.
            'storeScrappedImage' => $this->fmtStoreScrappedImage($value),
            default => $value,
        };
    }

    private function fmtPickLang($value, string $lang, string $fallback): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (!is_array($value)) {
            return '';
        }
        if (isset($value[$lang]) && is_string($value[$lang])) {
            return $value[$lang];
        }
        if (isset($value[$fallback]) && is_string($value[$fallback])) {
            return $value[$fallback];
        }
        $first = reset($value);
        return is_string($first) ? $first : '';
    }

    private function fmtClampInt($value, int $min, int $max): int
    {
        $v = is_numeric($value) ? (int) $value : 0;
        if ($max !== 0 && $max < $min) {
            return $v;
        }
        return max($min, min($max, $v));
    }

    private function fmtTruncate($value, int $max): string
    {
        $s = $value === null ? '' : (string) $value;
        return mb_substr($s, 0, $max);
    }

    private function fmtMapSize(string $value, string $default): string
    {
        $valid = ['tiny', 'small', 'medium', 'large', 'huge'];
        return in_array($value, $valid, true) ? $value : $default;
    }

    private function fmtStoreScrappedImage($value): ?string
    {
        if ($value === null) {
            return null;
        }
        return (string) $value;
    }

    /**
     * Mapping typeId DofusDB -> type/category KrosmozJDR.
     *
     * IMPORTANT: conserve la logique actuelle : si ResourceType::isDofusdbTypeAllowed($typeId),
     * on force en "resource" pour éviter de devoir modifier le code lors de nouveaux typeId.
     *
     * @return array{type:string,category:string}
     */
    private function mapItemTypeId(?int $typeId): array
    {
        if ($typeId !== null && ResourceType::isDofusdbTypeAllowed($typeId)) {
            return ['type' => 'resource', 'category' => 'resource'];
        }

        $typeMapping = [
            1 => ['type' => 'weapon', 'category' => 'weapon'],
            2 => ['type' => 'weapon', 'category' => 'weapon'],
            3 => ['type' => 'weapon', 'category' => 'weapon'],
            4 => ['type' => 'weapon', 'category' => 'weapon'],
            5 => ['type' => 'weapon', 'category' => 'weapon'],
            6 => ['type' => 'weapon', 'category' => 'weapon'],
            7 => ['type' => 'weapon', 'category' => 'weapon'],
            8 => ['type' => 'weapon', 'category' => 'weapon'],
            9 => ['type' => 'ring', 'category' => 'accessory'],
            10 => ['type' => 'amulet', 'category' => 'accessory'],
            11 => ['type' => 'belt', 'category' => 'accessory'],
            12 => ['type' => 'potion', 'category' => 'potion'],
            13 => ['type' => 'boots', 'category' => 'accessory'],
            14 => ['type' => 'hat', 'category' => 'accessory'],
            15 => ['type' => 'resource', 'category' => 'resource'],
            16 => ['type' => 'equipment', 'category' => 'equipment'],
            17 => ['type' => 'equipment', 'category' => 'equipment'],
            18 => ['type' => 'equipment', 'category' => 'equipment'],
            19 => ['type' => 'weapon', 'category' => 'weapon'],
            20 => ['type' => 'weapon', 'category' => 'weapon'],
            35 => ['type' => 'flower', 'category' => 'flower'],
            203 => ['type' => 'cosmetic', 'category' => 'cosmetic'],
            205 => ['type' => 'mount', 'category' => 'mount'],
        ];

        if ($typeId !== null && isset($typeMapping[$typeId])) {
            return $typeMapping[$typeId];
        }

        return ['type' => 'equipment', 'category' => 'equipment'];
    }

    private function fmtMapDofusdbItemType($value): string
    {
        $typeId = is_numeric($value) ? (int) $value : null;
        return $this->mapItemTypeId($typeId)['type'];
    }

    private function fmtMapDofusdbItemCategory($value): string
    {
        $typeId = is_numeric($value) ? (int) $value : null;
        return $this->mapItemTypeId($typeId)['category'];
    }
}

