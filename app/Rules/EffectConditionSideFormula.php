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
 * Valide une formule d’effet / condition avec placeholders caractéristiques.
 *
 * Formes acceptées :
 * - {@code [clé]} ou {@code [clé:caster]} → lanceur
 * - {@code [clé:target]} → cible
 * - ancienne forme {@code [caster:clé]} / {@code [target:clé]} (compat lecture)
 *
 * @example
 * 'params.conditions.*.left' => [new EffectConditionSideFormula]
 */
final class EffectConditionSideFormula implements ValidationRule
{
    /** Nouvelle forme clé[:acteur] + ancienne acteur:clé. */
    private const PLACEHOLDER_PATTERN = '/\[(?:(?:(caster|target):([a-zA-Z_][a-zA-Z0-9_]*))|([a-zA-Z_][a-zA-Z0-9_]*)(?::(caster|target))?)\]/';

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

        $probe = (string) preg_replace_callback(
            self::PLACEHOLDER_PATTERN,
            function (array $m) use ($allowed, &$unknown): string {
                // Groupe 1+2 = ancienne forme acteur:clé ; 3+4 = nouvelle clé[:acteur]
                $legacyKey = (string) ($m[2] ?? '');
                $suffixKey = (string) ($m[3] ?? '');
                $key = $legacyKey !== '' ? $legacyKey : $suffixKey;
                if ($key === '' || ! isset($allowed[$key])) {
                    $unknown[] = $key !== '' ? $key : $m[0];
                }

                return '1';
            },
            $value
        );

        if ($unknown !== []) {
            $fail('Caractéristique inconnue : '.implode(', ', array_unique($unknown)).'.');

            return;
        }

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
        return Cache::remember('effect_condition_side_placeholders_v2', 300, function (): array {
            $validator = app(CreatureFormulaPlaceholderValidator::class);
            $allowed = $validator->buildAllowedPlaceholderSet();

            foreach (['intel', 'strong', 'agi', 'chance', 'vitality', 'sagesse', 'force', 'wisdom', 'strength', 'agility', 'level', 'niveau'] as $alias) {
                $allowed[$alias] = true;
            }

            return $allowed;
        });
    }
}
