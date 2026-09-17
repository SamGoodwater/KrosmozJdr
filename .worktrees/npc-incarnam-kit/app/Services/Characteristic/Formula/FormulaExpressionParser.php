<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Parse et évalue les valeurs saisies à la main sur une entité (total explicite, bonus contextuel,
 * bonus d'équipement) selon la grammaire Krosmoz.
 *
 * Grammaire :
 * - un nombre simple : `12`, `-3`, `2.5`
 * - une formule encadrée : `{ expression }` avec suffixe d'arrondi optionnel
 *   - `}`  : arrondi normal
 *   - `}+` : arrondi supérieur
 *   - `}-` : arrondi inférieur
 * - dans l'expression, `[ident]` référence une caractéristique (clé BDD, nom court ou alias français)
 * - `[x-y]` est une fourchette entière et `[ndX]` (ou `ndX`) un dé : ce sont des *domaines*, qui
 *   produisent plusieurs valeurs possibles. Ils ne sont autorisés que sur le niveau.
 *
 * @example
 *   $parser->evaluate('{[niveau] / 3}+', ['level' => 7]);        // 3.0
 *   $parser->evaluate('{([vitality] - 10) / 2}-', ['vitality' => 15]); // 2.0
 *   $parser->enumerateOutcomes('{8 + [1d4]}', []);               // [9.0, 10.0, 11.0, 12.0]
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */
final class FormulaExpressionParser
{
    public const ROUNDING_NONE = 'none';

    public const ROUNDING_ROUND = 'round';

    public const ROUNDING_CEIL = 'ceil';

    public const ROUNDING_FLOOR = 'floor';

    public const KIND_EMPTY = 'empty';

    public const KIND_NUMBER = 'number';

    public const KIND_FORMULA = 'formula';

    /** Nombre maximal de combinaisons de domaines explorées (garde-fou). */
    private const MAX_COMBINATIONS = 400;

    /**
     * Alias français acceptés dans les formules. Résolus uniquement si l'identifiant brut
     * n'existe pas déjà dans la carte de variables (les colonnes réelles gardent la priorité).
     *
     * @var array<string, string>
     */
    private const IDENTIFIER_ALIASES = [
        'niveau' => 'level',
        'vitalite' => 'vitality',
        'force' => 'strength',
        'agilite' => 'agility',
        'sagesse_score' => 'wisdom',
    ];

    /** Référence de caractéristique : [ident]. */
    private const PATTERN_IDENTIFIER = '/\[([a-zA-Z_][a-zA-Z0-9_]*)\]/';

    /** Fourchette entière : [x-y]. */
    private const PATTERN_RANGE = '/\[\s*(\d+)\s*(?:-|–|\.\.)\s*(\d+)\s*\]/';

    /** Dé entre crochets : [ndX]. */
    private const PATTERN_DICE_BRACKET = '/\[\s*(\d*)\s*[dD]\s*(\d+)\s*\]/';

    /** Dé nu : ndX (hors crochets, et jamais après une variable comme [level]d[life_dice]). */
    private const PATTERN_DICE_BARE = '/(?<![\w\]])(\d*)[dD](\d+)(?![\w])/';

    public function __construct(
        private readonly SafeExpressionEvaluator $expressionEvaluator
    ) {}

    /**
     * Décompose une valeur saisie.
     *
     * @return array{
     *   raw: string,
     *   kind: string,
     *   number: float|null,
     *   expression: string,
     *   rounding: string,
     *   braced: bool,
     *   domains: list<array{token: string, kind: string, label: string, values: list<int>}>,
     *   identifiers: list<string>
     * }
     */
    public function parse(?string $raw): array
    {
        $value = trim((string) $raw);
        $empty = [
            'raw' => $value,
            'kind' => self::KIND_EMPTY,
            'number' => null,
            'expression' => '',
            'rounding' => self::ROUNDING_NONE,
            'braced' => false,
            'domains' => [],
            'identifiers' => [],
        ];

        if ($value === '') {
            return $empty;
        }

        if (is_numeric($value)) {
            return [...$empty, 'kind' => self::KIND_NUMBER, 'number' => (float) $value];
        }

        $braced = false;
        $rounding = self::ROUNDING_NONE;
        $expression = $value;

        if (str_starts_with($value, '{')) {
            $closing = strrpos($value, '}');
            if ($closing === false) {
                return [...$empty, 'kind' => self::KIND_FORMULA, 'expression' => ltrim($value, '{')];
            }
            $braced = true;
            $expression = substr($value, 1, $closing - 1);
            $suffix = trim(substr($value, $closing + 1));
            $rounding = match ($suffix) {
                '+' => self::ROUNDING_CEIL,
                '-' => self::ROUNDING_FLOOR,
                default => self::ROUNDING_ROUND,
            };
        }

        return [
            'raw' => $value,
            'kind' => self::KIND_FORMULA,
            'number' => null,
            'expression' => trim($expression),
            'rounding' => $rounding,
            'braced' => $braced,
            'domains' => $this->extractDomains($expression),
            'identifiers' => $this->extractIdentifiers($expression),
        ];
    }

    public function isFormula(?string $raw): bool
    {
        return $this->parse($raw)['kind'] === self::KIND_FORMULA;
    }

    public function isEmpty(?string $raw): bool
    {
        return $this->parse($raw)['kind'] === self::KIND_EMPTY;
    }

    /**
     * Évalue une valeur saisie. Les domaines sont résolus selon $diceMode (borne minimale par défaut,
     * afin de rester déterministe pour l'affichage).
     *
     * @param  array<string, int|float>  $variables
     */
    public function evaluate(
        ?string $raw,
        array $variables = [],
        string $diceMode = SafeExpressionEvaluator::DICE_MODE_MIN
    ): ?float {
        $parsed = $this->parse($raw);

        if ($parsed['kind'] === self::KIND_EMPTY) {
            return null;
        }
        if ($parsed['kind'] === self::KIND_NUMBER) {
            return $parsed['number'];
        }

        $expression = $this->substituteDomains($parsed['expression'], $parsed['domains'], $diceMode);
        $expression = $this->substituteIdentifiers($expression, $variables);
        $result = $this->expressionEvaluator->evaluate($expression, $diceMode);

        return $result === null ? null : $this->applyRounding($result, $parsed['rounding']);
    }

    /**
     * Liste triée et dédoublonnée de toutes les valeurs possibles d'une saisie contenant des domaines.
     * Sans domaine, retourne l'unique valeur évaluée.
     *
     * @param  array<string, int|float>  $variables
     * @return list<float>
     */
    public function enumerateOutcomes(?string $raw, array $variables = [], int $maxOutcomes = 20): array
    {
        $parsed = $this->parse($raw);

        if ($parsed['kind'] === self::KIND_EMPTY) {
            return [];
        }
        if ($parsed['kind'] === self::KIND_NUMBER) {
            return [(float) $parsed['number']];
        }
        if ($parsed['domains'] === []) {
            $single = $this->evaluate($raw, $variables, SafeExpressionEvaluator::DICE_MODE_MIN);

            return $single === null ? [] : [$single];
        }

        $outcomes = [];
        foreach ($this->domainCombinations($parsed['domains']) as $combination) {
            $expression = $parsed['expression'];
            foreach ($combination as $token => $chosen) {
                $expression = str_replace($token, (string) $chosen, $expression);
            }
            $expression = $this->substituteIdentifiers($expression, $variables);
            $result = $this->expressionEvaluator->evaluate($expression, SafeExpressionEvaluator::DICE_MODE_MIN);
            if ($result === null) {
                continue;
            }
            $rounded = $this->applyRounding($result, $parsed['rounding']);
            $outcomes[(string) $rounded] = $rounded;
        }

        $values = array_values($outcomes);
        sort($values);

        return $this->sampleEvenly($values, max(1, $maxOutcomes));
    }

    /**
     * Remplace les identifiants par leur valeur pour affichage (décomposition, popover).
     *
     * @param  array<string, int|float>  $variables
     */
    public function substituteForDisplay(?string $raw, array $variables = []): ?string
    {
        $parsed = $this->parse($raw);
        if ($parsed['kind'] !== self::KIND_FORMULA) {
            return null;
        }

        return $this->substituteIdentifiers($parsed['expression'], $variables);
    }

    /**
     * Valide une saisie.
     *
     * @param  list<string>|null  $allowedIdentifiers  Si fourni, tout identifiant hors liste est une erreur
     * @return list<string> Liste d'erreurs (vide si valide)
     */
    public function validate(?string $raw, bool $allowDomains = false, ?array $allowedIdentifiers = null): array
    {
        $value = trim((string) $raw);
        if ($value === '' || is_numeric($value)) {
            return [];
        }

        $errors = [];

        if (str_starts_with($value, '{')) {
            if (! str_contains($value, '}')) {
                return ['Accolade fermante « } » manquante.'];
            }
            $suffix = trim(substr($value, (int) strrpos($value, '}') + 1));
            if (! in_array($suffix, ['', '+', '-'], true)) {
                $errors[] = sprintf('Suffixe d\'arrondi « %s » inconnu (attendu : rien, + ou -).', $suffix);
            }
        } else {
            $errors[] = 'Une formule doit être encadrée par des accolades, par exemple {[niveau] / 3}+.';
        }

        $parsed = $this->parse($value);

        if (! $allowDomains && $parsed['domains'] !== []) {
            $errors[] = 'Fourchettes et dés ne sont autorisés que sur le niveau.';
        }

        $withoutDomains = $this->substituteDomains(
            $parsed['expression'],
            $parsed['domains'],
            SafeExpressionEvaluator::DICE_MODE_MIN
        );

        if (preg_match_all('/\[([^\]]*)\]/', $withoutDomains, $matches)) {
            foreach ($matches[1] as $identifier) {
                if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string) $identifier)) {
                    $errors[] = sprintf('Référence « [%s] » invalide (attendu une clé de caractéristique).', $identifier);
                }
            }
        }

        if ($allowedIdentifiers !== null) {
            foreach ($parsed['identifiers'] as $identifier) {
                $canonical = self::IDENTIFIER_ALIASES[$identifier] ?? $identifier;
                if (! in_array($identifier, $allowedIdentifiers, true) && ! in_array($canonical, $allowedIdentifiers, true)) {
                    $errors[] = sprintf('Caractéristique « [%s] » inconnue.', $identifier);
                }
            }
        }

        $probe = (string) preg_replace(self::PATTERN_IDENTIFIER, '0', $withoutDomains);
        foreach ($this->expressionEvaluator->validate($probe) as $expressionError) {
            $errors[] = $expressionError;
        }
        if ($errors === [] && $this->expressionEvaluator->evaluate($probe, SafeExpressionEvaluator::DICE_MODE_MIN) === null) {
            $errors[] = 'Expression mathématique invalide.';
        }

        return array_values(array_unique($errors));
    }

    /**
     * Identifiants référencés, alias français résolus vers leur clé canonique.
     *
     * @return list<string>
     */
    public function canonicalIdentifiers(?string $raw): array
    {
        $out = [];
        foreach ($this->parse($raw)['identifiers'] as $identifier) {
            $canonical = self::IDENTIFIER_ALIASES[$identifier] ?? $identifier;
            if (! in_array($canonical, $out, true)) {
                $out[] = $canonical;
            }
        }

        return $out;
    }

    /**
     * @return array<string, string>
     */
    public static function identifierAliases(): array
    {
        return self::IDENTIFIER_ALIASES;
    }

    /**
     * @return list<array{token: string, kind: string, label: string, values: list<int>}>
     */
    private function extractDomains(string $expression): array
    {
        $domains = [];

        if (preg_match_all(self::PATTERN_RANGE, $expression, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $min = (int) $match[1];
                $max = (int) $match[2];
                $domains[$match[0]] = [
                    'token' => $match[0],
                    'kind' => 'range',
                    'label' => min($min, $max).'-'.max($min, $max),
                    'values' => range(min($min, $max), max($min, $max)),
                ];
            }
        }

        foreach ([self::PATTERN_DICE_BRACKET, self::PATTERN_DICE_BARE] as $pattern) {
            $probe = $pattern === self::PATTERN_DICE_BARE
                ? (string) preg_replace(self::PATTERN_DICE_BRACKET, '', $expression)
                : $expression;
            if (! preg_match_all($pattern, $probe, $matches, PREG_SET_ORDER)) {
                continue;
            }
            foreach ($matches as $match) {
                if (isset($domains[$match[0]])) {
                    continue;
                }
                $count = $match[1] === '' ? 1 : max(1, (int) $match[1]);
                $faces = max(1, (int) $match[2]);
                $domains[$match[0]] = [
                    'token' => $match[0],
                    'kind' => 'dice',
                    'label' => $count.'d'.$faces,
                    'values' => range($count, $count * $faces),
                ];
            }
        }

        return array_values($domains);
    }

    /**
     * @return list<string>
     */
    private function extractIdentifiers(string $expression): array
    {
        $withoutDomains = (string) preg_replace(
            [self::PATTERN_RANGE, self::PATTERN_DICE_BRACKET],
            '0',
            $expression
        );

        $out = [];
        if (preg_match_all(self::PATTERN_IDENTIFIER, $withoutDomains, $matches)) {
            foreach ($matches[1] as $identifier) {
                if (! in_array($identifier, $out, true)) {
                    $out[] = $identifier;
                }
            }
        }

        return $out;
    }

    /**
     * @param  list<array{token: string, kind: string, label: string, values: list<int>}>  $domains
     */
    private function substituteDomains(string $expression, array $domains, string $diceMode): string
    {
        foreach ($domains as $domain) {
            $values = $domain['values'];
            $chosen = match ($diceMode) {
                SafeExpressionEvaluator::DICE_MODE_MAX => end($values),
                SafeExpressionEvaluator::DICE_MODE_AVERAGE => $values[intdiv(count($values), 2)],
                SafeExpressionEvaluator::DICE_MODE_ROLL => $values[array_rand($values)],
                default => $values[0],
            };
            $expression = str_replace($domain['token'], (string) $chosen, $expression);
        }

        return $expression;
    }

    /**
     * Produit les combinaisons de valeurs de domaines (plafonnées).
     *
     * @param  list<array{token: string, kind: string, label: string, values: list<int>}>  $domains
     * @return list<array<string, int>>
     */
    private function domainCombinations(array $domains): array
    {
        $combinations = [[]];
        foreach ($domains as $domain) {
            $next = [];
            foreach ($combinations as $combination) {
                foreach ($domain['values'] as $value) {
                    if (count($next) >= self::MAX_COMBINATIONS) {
                        break 2;
                    }
                    $next[] = $combination + [$domain['token'] => $value];
                }
            }
            $combinations = $next;
        }

        return $combinations;
    }

    /**
     * @param  list<float>  $values
     * @return list<float>
     */
    private function sampleEvenly(array $values, int $max): array
    {
        $count = count($values);
        if ($count <= $max) {
            return $values;
        }

        $out = [];
        for ($i = 0; $i < $max; $i++) {
            $index = (int) round($i * ($count - 1) / ($max - 1 ?: 1));
            $out[$index] = $values[$index];
        }
        ksort($out);

        return array_values($out);
    }

    /**
     * @param  array<string, int|float>  $variables
     */
    private function substituteIdentifiers(string $expression, array $variables): string
    {
        return (string) preg_replace_callback(
            self::PATTERN_IDENTIFIER,
            static function (array $matches) use ($variables): string {
                $identifier = $matches[1];
                if (array_key_exists($identifier, $variables)) {
                    return (string) (float) $variables[$identifier];
                }
                $alias = self::IDENTIFIER_ALIASES[$identifier] ?? null;
                if ($alias !== null && array_key_exists($alias, $variables)) {
                    return (string) (float) $variables[$alias];
                }

                return '0';
            },
            $expression
        );
    }

    private function applyRounding(float $value, string $rounding): float
    {
        return match ($rounding) {
            self::ROUNDING_CEIL => (float) ceil($value),
            self::ROUNDING_FLOOR => (float) floor($value),
            self::ROUNDING_ROUND => (float) round($value),
            default => $value,
        };
    }
}
