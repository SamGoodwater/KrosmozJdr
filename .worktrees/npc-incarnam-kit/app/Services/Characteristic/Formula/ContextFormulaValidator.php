<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Valide une formule / valeur contextuelle saisie sur une créature.
 *
 * Règles :
 * - nombre simple ou formule `{...}` avec suffixe d'arrondi
 * - domaines `[x-y]` / `[ndX]` interdits (réservés au niveau)
 * - identifiants connus uniquement
 * - détection de cycle simple si la clé courante se référence elle-même
 *
 * @example
 *   $v->validate('{[niveau] / 3}+', 'armor_class_creature');
 */
final class ContextFormulaValidator
{
    public function __construct(
        private readonly FormulaExpressionParser $parser,
        private readonly CreatureFormulaPlaceholderValidator $placeholders
    ) {}

    /**
     * @return list<string>
     */
    public function validate(?string $raw, ?string $selfKey = null): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $allowed = array_keys($this->placeholders->buildAllowedPlaceholderSet());
        $errors = $this->parser->validate($raw, false, $allowed);

        if ($selfKey !== null && $selfKey !== '') {
            $canonical = $this->parser->canonicalIdentifiers($raw);
            $selfShort = preg_replace('/_(creature|object|spell)$/', '', $selfKey) ?: $selfKey;
            foreach ($canonical as $id) {
                if ($id === $selfKey || $id === $selfShort) {
                    $errors[] = sprintf('Référence circulaire interdite : [%s].', $id);
                }
            }
        }

        return array_values(array_unique($errors));
    }
}
