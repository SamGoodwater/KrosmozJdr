<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Config;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;

/**
 * Valide un mapping de conversion avant son exécution.
 *
 * @example
 * $errors = $validator->validate('monster', $config['mapping']);
 */
final class ScrappingMappingValidator
{
    public function __construct(
        private readonly FormatterApplicator $formatters,
        private readonly CharacteristicGetterService $characteristics,
        private readonly CharacteristicFormulaService $formulas,
    ) {}

    /**
     * @return list<array{path:string,message:string}>
     */
    public function validate(string $entity, mixed $mapping): array
    {
        if (! is_array($mapping) || $mapping === []) {
            return [['path' => 'mapping', 'message' => 'Le mapping est vide.']];
        }

        $errors = [];
        $characteristicEntity = $entity === 'breed' ? 'class' : $entity;

        foreach (array_values($mapping) as $index => $rule) {
            $basePath = "mapping.{$index}";
            if (! is_array($rule)) {
                $errors[] = ['path' => $basePath, 'message' => 'La règle doit être un objet.'];

                continue;
            }

            $fromPath = $rule['from']['path'] ?? null;
            if (! is_string($fromPath) || trim($fromPath) === '') {
                $errors[] = ['path' => "{$basePath}.from.path", 'message' => 'Le chemin source est requis.'];
            }

            $targets = $rule['to'] ?? null;
            if (! is_array($targets) || $targets === []) {
                $errors[] = ['path' => "{$basePath}.to", 'message' => 'Au moins une cible est requise.'];
            } else {
                foreach ($targets as $targetIndex => $target) {
                    if (! is_array($target)
                        || ! is_string($target['model'] ?? null)
                        || trim((string) $target['model']) === ''
                        || ! is_string($target['field'] ?? null)
                        || trim((string) $target['field']) === '') {
                        $errors[] = [
                            'path' => "{$basePath}.to.{$targetIndex}",
                            'message' => 'La cible doit définir model et field.',
                        ];
                    }
                }
            }

            $formatterNames = [];
            foreach (is_array($rule['formatters'] ?? null) ? $rule['formatters'] : [] as $formatterIndex => $formatter) {
                $name = is_array($formatter) ? ($formatter['name'] ?? null) : null;
                if (is_string($name)) {
                    $formatterNames[] = $name;
                }
                if (! is_string($name) || ! $this->formatters->supports($name)) {
                    $errors[] = [
                        'path' => "{$basePath}.formatters.{$formatterIndex}",
                        'message' => 'Formatter inconnu : '.(is_string($name) ? $name : '(absent)').'.',
                    ];
                }
            }

            $characteristicKey = $rule['characteristic_key'] ?? null;
            if ($characteristicKey === null || $characteristicKey === '') {
                continue;
            }
            $usesCharacteristicConversion = array_intersect($formatterNames, [
                'convertCharacteristic',
                'dofusdb_level',
                'dofusdb_life',
                'dofusdb_attribute',
                'dofusdb_ini',
                'clampToCharacteristic',
            ]) !== [];
            if (! $usesCharacteristicConversion) {
                continue;
            }
            if (! is_string($characteristicKey)) {
                $errors[] = ['path' => "{$basePath}.characteristic_key", 'message' => 'La clé caractéristique doit être une chaîne.'];

                continue;
            }

            $definition = $this->characteristics->getDefinition($characteristicKey, $characteristicEntity);
            if ($definition === null) {
                $errors[] = [
                    'path' => "{$basePath}.characteristic_key",
                    'message' => "Caractéristique {$characteristicKey} introuvable pour {$characteristicEntity}.",
                ];

                continue;
            }

            $formula = $definition['conversion_formula'] ?? null;
            foreach ($this->formulas->validateFormula(is_string($formula) ? $formula : null) as $formulaError) {
                $errors[] = [
                    'path' => "{$basePath}.characteristic_key",
                    'message' => "Formule {$characteristicKey} invalide : {$formulaError}",
                ];
            }
        }

        return $errors;
    }

    public function validateOrFail(string $entity, mixed $mapping): void
    {
        $errors = $this->validate($entity, $mapping);
        if ($errors === []) {
            return;
        }

        $first = $errors[0];
        throw new \InvalidArgumentException("Mapping invalide ({$first['path']}) : {$first['message']}");
    }
}
