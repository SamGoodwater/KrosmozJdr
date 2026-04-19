<?php

declare(strict_types=1);

namespace App\Support\Characteristics;

/**
 * Utilitaires JSON pour les définitions (clés "_" = méta / commentaires, ignorées au seed).
 */
final class CharacteristicDefinitionJson
{
    /**
     * @param  array<mixed>|scalar|null  $data
     * @return array<mixed>|scalar|null
     */
    public static function stripUnderscoreKeys(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }
        $out = [];
        foreach ($data as $k => $v) {
            if (is_string($k) && str_starts_with($k, '_')) {
                continue;
            }
            $out[$k] = is_array($v) ? self::stripUnderscoreKeys($v) : $v;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function encodePretty(array $data): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        )."\n";
    }

    /**
     * @return array<string, mixed>
     */
    public static function decodeFile(string $absolutePath): array
    {
        $raw = file_get_contents($absolutePath);
        if ($raw === false || $raw === '') {
            throw new \RuntimeException('Fichier vide ou illisible : '.$absolutePath);
        }
        /** @var array<string, mixed>|null $decoded */
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            throw new \RuntimeException('JSON invalide : '.$absolutePath);
        }

        return $decoded;
    }
}
