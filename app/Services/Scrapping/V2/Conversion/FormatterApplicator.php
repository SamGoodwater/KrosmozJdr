<?php

namespace App\Services\Scrapping\V2\Conversion;

/**
 * Applique les formatters purs utilisés par la conversion V2.
 *
 * Formatters supportés : toString, pickLang, toInt, nullableInt, clampInt, mapSizeToKrosmoz,
 * storeScrappedImage, truncate. Si DofusDbConversionFormulas est injecté : dofusdb_level,
 * dofusdb_life, dofusdb_attribute, dofusdb_ini (formules et limites en BDD).
 */
final class FormatterApplicator
{
    public function __construct(
        private readonly ?DofusDbConversionFormulas $conversionFormulas = null
    ) {
    }

    /**
     * @param array<string, mixed> $args
     * @param array<string, mixed> $raw
     * @param array{entityType?: string, lang?: string} $context
     * @return mixed
     */
    public function apply(string $name, mixed $value, array $args, array $raw, array $context = []): mixed
    {
        $entityType = (string) ($context['entityType'] ?? 'monster');

        return match ($name) {
            'toString' => $value === null ? '' : (string) $value,
            'pickLang' => $this->pickLang($value, (string) ($args['lang'] ?? 'fr'), (string) ($args['fallback'] ?? 'fr')),
            'toInt' => is_numeric($value) ? (int) $value : 0,
            'nullableInt' => $value === null ? null : (is_numeric($value) ? (int) $value : null),
            'clampInt' => $this->clampInt($value, (int) ($args['min'] ?? 0), (int) ($args['max'] ?? 0)),
            'mapSizeToKrosmoz' => $this->mapSize((string) $value, (string) ($args['default'] ?? 'medium')),
            'storeScrappedImage' => $value === null ? null : (string) $value,
            'truncate' => $this->truncate($value, (int) ($args['max'] ?? 255)),
            'dofusdb_level' => $this->conversionFormulas !== null
                ? $this->conversionFormulas->convertLevel($this->numericValue($value), $entityType)
                : $value,
            'dofusdb_life' => $this->conversionFormulas !== null
                ? $this->applyDofusdbLife($value, $raw, $args, $entityType)
                : $value,
            'dofusdb_attribute' => $this->conversionFormulas !== null && isset($args['characteristicId'])
                ? $this->conversionFormulas->convertAttribute((string) $args['characteristicId'], $value, $entityType)
                : $value,
            'dofusdb_ini' => $this->conversionFormulas !== null
                ? $this->conversionFormulas->convertInitiative($this->numericValue($value), $entityType)
                : $value,
            default => $value,
        };
    }

    public function supports(string $name): bool
    {
        $base = [
            'toString',
            'pickLang',
            'toInt',
            'nullableInt',
            'clampInt',
            'mapSizeToKrosmoz',
            'storeScrappedImage',
            'truncate',
        ];
        if ($this->conversionFormulas !== null) {
            $base = array_merge($base, ['dofusdb_level', 'dofusdb_life', 'dofusdb_attribute', 'dofusdb_ini']);
        }

        return in_array($name, $base, true);
    }

    private function numericValue(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function applyDofusdbLife(mixed $value, array $raw, array $args, string $entityType): int
    {
        $levelPath = (string) ($args['levelPath'] ?? 'grades.0.level');
        $levelRaw = $this->getByPath($raw, $levelPath);
        $levelKrosmoz = $this->conversionFormulas->convertLevel($this->numericValue($levelRaw), $entityType);

        return $this->conversionFormulas->convertLife($this->numericValue($value), $levelKrosmoz, $entityType);
    }

    /**
     * @param array<string, mixed> $data
     * @return mixed
     */
    private function getByPath(array $data, string $path): mixed
    {
        $parts = explode('.', $path);
        $cur = $data;
        foreach ($parts as $part) {
            if (!is_array($cur)) {
                return null;
            }
            $key = ctype_digit($part) ? (int) $part : $part;
            $cur = $cur[$key] ?? null;
        }

        return $cur;
    }

    private function truncate(mixed $value, int $max): string
    {
        $s = $value === null ? '' : (string) $value;
        if ($max <= 0) {
            return $s;
        }
        if (mb_strlen($s) <= $max) {
            return $s;
        }

        return mb_substr($s, 0, $max);
    }

    private function pickLang(mixed $value, string $lang, string $fallback): string
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

    private function clampInt(mixed $value, int $min, int $max): int
    {
        $v = is_numeric($value) ? (int) $value : 0;
        if ($max !== 0 && $max < $min) {
            return $v;
        }

        return max($min, min($max, $v));
    }

    private function mapSize(string $value, string $default): string
    {
        $valid = ['tiny', 'small', 'medium', 'large', 'huge'];

        return in_array($value, $valid, true) ? $value : $default;
    }
}
