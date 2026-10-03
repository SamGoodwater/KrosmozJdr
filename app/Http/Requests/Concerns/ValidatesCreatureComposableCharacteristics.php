<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Services\Characteristic\Formula\ContextFormulaValidator;
use App\Services\Characteristic\Formula\CreatureFormulaPlaceholderValidator;
use App\Services\Characteristic\Formula\FormulaExpressionParser;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Creature\CreatureComposableCharacteristicsPersister;
use App\Support\Creature\CreatureComposableColumns;
use Illuminate\Validation\Validator;

/**
 * Règles et validation métier pour totaux + bonus contextuels créature.
 */
trait ValidatesCreatureComposableCharacteristics
{
    /**
     * @return array<string, list<string>>
     */
    protected function creatureComposableFieldRules(): array
    {
        $rules = [];
        foreach (CreatureComposableColumns::all() as $column) {
            $rules[$column] = ['sometimes', 'nullable', 'string'];
            $rules[CreatureComposableColumns::contextColumn($column)] = ['sometimes', 'nullable', 'string'];
        }

        return $rules;
    }

    protected function validateCreatureComposableFields(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $contextValidator = app(ContextFormulaValidator::class);
        $limitService = app(CharacteristicLimitService::class);
        $getter = app(CharacteristicGetterService::class);

        foreach (CreatureComposableColumns::all() as $column) {
            $this->validateOneComposableField(
                $validator,
                $contextValidator,
                $limitService,
                $getter,
                $column,
                false
            );
            $this->validateOneComposableField(
                $validator,
                $contextValidator,
                $limitService,
                $getter,
                CreatureComposableColumns::contextColumn($column),
                true,
                $column
            );
        }
    }

    /**
     * @return array<string, string|null>
     */
    public function validatedComposableCharacteristics(): array
    {
        return app(CreatureComposableCharacteristicsPersister::class)
            ->extractPayload($this->validated());
    }

    private function validateOneComposableField(
        Validator $validator,
        ContextFormulaValidator $contextValidator,
        CharacteristicLimitService $limitService,
        CharacteristicGetterService $getter,
        string $field,
        bool $isContext,
        ?string $baseColumn = null,
    ): void {
        if (! $this->exists($field)) {
            return;
        }

        $raw = $this->input($field);
        if ($raw === null || (is_string($raw) && trim($raw) === '')) {
            return;
        }

        $stringValue = is_string($raw) ? $raw : (string) $raw;
        $column = $isContext ? ($baseColumn ?? '') : $field;
        if ($column === '') {
            return;
        }

        $characteristicKey = $this->characteristicKeyForColumn($getter, $column);
        $formulaErrors = $contextValidator->validate($stringValue, $characteristicKey);
        foreach ($formulaErrors as $message) {
            $validator->errors()->add($field, $message);
        }

        if ($formulaErrors !== []) {
            return;
        }

        if (! is_numeric(trim($stringValue))) {
            return;
        }

        $numeric = (int) (float) trim($stringValue);
        $limitResult = $limitService->validateSingle($characteristicKey, $numeric, 'creature');
        if (! $limitResult->isValid()) {
            foreach ($limitResult->getErrors() as $err) {
                $validator->errors()->add($field, $err['message'] ?? 'Valeur hors limites.');
            }
        }
    }

    protected function validateCreatureLevelField(Validator $validator, string $field = 'level'): void
    {
        if (! $this->exists($field)) {
            return;
        }

        $raw = $this->input($field);
        if ($raw === null || (is_string($raw) && trim($raw) === '')) {
            return;
        }

        $stringValue = is_string($raw) ? $raw : (string) $raw;
        if (is_numeric(trim($stringValue))) {
            $limitResult = app(CharacteristicLimitService::class)->validateSingle(
                'level_creature',
                (int) (float) trim($stringValue),
                'creature'
            );
            if (! $limitResult->isValid()) {
                foreach ($limitResult->getErrors() as $err) {
                    $validator->errors()->add($field, $err['message'] ?? 'Niveau hors limites.');
                }
            }

            return;
        }

        $parser = app(FormulaExpressionParser::class);
        $allowed = array_keys(app(CreatureFormulaPlaceholderValidator::class)->buildAllowedPlaceholderSet());
        foreach ($parser->validate($stringValue, true, $allowed) as $message) {
            $validator->errors()->add($field, $message);
        }
    }

    private function characteristicKeyForColumn(CharacteristicGetterService $getter, string $column): string
    {
        $def = $getter->getDefinitionByField($column, 'creature');

        return is_array($def) && isset($def['key']) && is_string($def['key']) && $def['key'] !== ''
            ? $def['key']
            : $column.'_creature';
    }
}
