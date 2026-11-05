<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Characteristic\Formula\CreatureFormulaPlaceholderValidator;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use App\Support\Spell\EffectConditionParams;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;

/**
 * Valide un côté de condition (formule avec éventuels {@code [caster:clé]} / {@code [target:clé]}).
 *
 * Remplace chaque crochet par {@code 1}, puis délègue à {@see SafeExpressionEvaluator}.
 *
 * @example
 * 'params.conditions.*.left' => [new EffectConditionSideFormula(requireActorPrefix: true)]
 */
final class EffectConditionSideFormula implements ValidationRule
{
    private const PLACEHOLDER_PATTERN = '/\[(?:(caster|target):)?([a-zA-Z_][a-zA-Z0-9_]*)\]/';

    public function __construct(
        private readonly bool $requireActorPrefix = false,
        private readonly bool $allowBareCharacteristic = true,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }
        if (! is_string($value)) {
            $fail('La formule doit être une chaîne.');

            return;
        }
        if (mb_strlen($value) > EffectConditionParams::SIDE_MAX_LENGTH) {
            $fail('La formule dépasse '.EffectConditionParams::SIDE_MAX_LENGTH.' caractères.');

            return;
        }

        $allowed = $this->allowedPlaceholders();
        $unknown = [];
        $missingPrefix = false;

        $probe = (string) preg_replace_callback(
            self::PLACEHOLDER_PATTERN,
            function (array $m) use ($allowed, &$unknown, &$missingPrefix): string {
                $prefix = $m[1] ?? '';
                $key = $m[2] ?? '';
                if ($this->requireActorPrefix && $prefix === '') {
                    $missingPrefix = true;

                    return '1';
                }
                if ($prefix !== '' && ! in_array($prefix, ['caster', 'target'], true)) {
                    $unknown[] = $m[0];

                    return '1';
                }
                if ($prefix === '' && ! $this->allowBareCharacteristic) {
                    $missingPrefix = true;

                    return '1';
                }
                if (! isset($allowed[$key])) {
                    $unknown[] = $key;
                }

                return '1';
            },
            $value
        );

        if ($missingPrefix) {
            $fail('Chaque caractéristique doit indiquer le lanceur ou la cible : [caster:…] ou [target:…].');

            return;
        }
        if ($unknown !== []) {
            $fail('Caractéristique inconnue : '.implode(', ', array_unique($unknown)).'.');

            return;
        }

        // Crochets restants (ex. [1-4] ou syntaxe invalide) → rejet
        if (preg_match('/\[/', $probe) === 1) {
            $fail('Syntaxe de crochets invalide dans la formule.');

            return;
        }

        $evaluator = app(SafeExpressionEvaluator::class);
        $errors = $evaluator->validate($probe);
        if ($errors !== []) {
            $fail($errors[0]);
        }
    }

    /**
     * @return array<string, true>
     */
    private function allowedPlaceholders(): array
    {
        return Cache::remember('effect_condition_side_placeholders_v1', 300, function (): array {
            $validator = app(CreatureFormulaPlaceholderValidator::class);
            $allowed = $validator->buildAllowedPlaceholderSet();

            // Alias courts utilisés dans les formules de sorts (dégâts).
            foreach (['intel', 'strong', 'agi', 'chance', 'vitality', 'sagesse', 'force', 'wisdom', 'strength', 'agility', 'level', 'niveau'] as $alias) {
                $allowed[$alias] = true;
            }

            return $allowed;
        });
    }
}
