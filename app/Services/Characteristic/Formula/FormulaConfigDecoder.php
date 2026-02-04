<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Décodage / encodage du champ formula des caractéristiques.
 *
 * Le champ peut contenir :
 * - Une formule simple (chaîne) : ex. "[vitality]*10+[level]*2"
 * - Un tableau par caractéristique (JSON) : ex. {"characteristic":"level","1":0,"7":2,"14":4}
 *
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 */
final class FormulaConfigDecoder
{
    /**
     * @return array{type: 'formula', expression: string}|array{type: 'table', characteristic: string, entries: list<array{from: int, value: int|float|string}>}
     */
    public static function decode(?string $formula): array
    {
        if ($formula === null || $formula === '') {
            return ['type' => 'formula', 'expression' => ''];
        }

        $trimmed = trim($formula);
        if ($trimmed === '') {
            return ['type' => 'formula', 'expression' => ''];
        }

        if (str_starts_with($trimmed, '{')) {
            $decoded = json_decode($trimmed, true);
            if (! is_array($decoded)) {
                return ['type' => 'formula', 'expression' => $formula];
            }
            $char = $decoded['characteristic'] ?? null;
            if (! is_string($char) || $char === '') {
                return ['type' => 'formula', 'expression' => $formula];
            }
            $entries = [];
            foreach ($decoded as $key => $value) {
                if ($key === 'characteristic') {
                    continue;
                }
                if (is_numeric($key)) {
                    $from = (int) $key;
                    $entries[] = [
                        'from' => $from,
                        'value' => is_numeric($value) ? (float) $value : (string) $value,
                    ];
                }
            }
            usort($entries, static fn (array $a, array $b) => $a['from'] <=> $b['from']);

            return [
                'type' => 'table',
                'characteristic' => $char,
                'entries' => $entries,
            ];
        }

        return ['type' => 'formula', 'expression' => $formula];
    }

    /**
     * @param array{type: 'formula', expression: string}|array{type: 'table', characteristic: string, entries: list<array{from: int, value: int|float|string}>} $decoded
     */
    public static function encode(array $decoded): string
    {
        if ($decoded['type'] === 'formula') {
            return (string) ($decoded['expression'] ?? '');
        }

        if ($decoded['type'] !== 'table' || empty($decoded['entries'])) {
            return '';
        }

        $char = $decoded['characteristic'] ?? '';
        $obj = ['characteristic' => $char];
        foreach ($decoded['entries'] as $entry) {
            $from = (int) ($entry['from'] ?? 0);
            $value = $entry['value'] ?? 0;
            $obj[(string) $from] = is_int($value) || is_float($value) ? $value : (string) $value;
        }

        $json = json_encode($obj, JSON_UNESCAPED_UNICODE);
        return $json !== false ? $json : '';
    }

    public static function isTable(?string $formula): bool
    {
        $decoded = self::decode($formula);
        return $decoded['type'] === 'table';
    }
}
