<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Services\Creature\CreatureExpertiseValidator;
use App\Support\Creature\CreatureMasteryColumns;
use Illuminate\Validation\Validator;

/**
 * Règles et validation métier pour colonnes *_mastery et intimidation_ability.
 */
trait ValidatesCreatureSkillMasteries
{
    /**
     * @return array<string, list<string>>
     */
    protected function creatureSkillMasteryFieldRules(): array
    {
        $rules = [
            'intimidation_ability' => ['sometimes', 'nullable', 'string', 'in:strength,chance'],
        ];
        foreach (CreatureMasteryColumns::all() as $column) {
            $rules[$column] = ['sometimes', 'nullable', 'integer', 'min:0', 'max:2'];
        }

        return $rules;
    }

    protected function validateCreatureSkillMasteries(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $creature = $this->resolveCreatureForMasteryValidation();
        if ($creature === null) {
            return;
        }

        $input = $validator->getData();
        $hasMasteryChange = array_key_exists('intimidation_ability', $input);
        foreach (CreatureMasteryColumns::all() as $column) {
            if (array_key_exists($column, $input)) {
                $hasMasteryChange = true;
                break;
            }
        }
        if (! $hasMasteryChange) {
            return;
        }

        $expertiseValidator = app(CreatureExpertiseValidator::class);
        $level = $expertiseValidator->resolveLevel($creature, $input);
        $merged = $expertiseValidator->mergeMasteryInput($creature, $input);

        foreach ($expertiseValidator->validate($level, $merged) as $message) {
            $validator->errors()->add('mastery', $message);
        }
    }

    /**
     * Extrait les champs maîtrise / intimidation_ability validés pour persistance créature.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, int|string>
     */
    public function validatedSkillMasteryPayload(array $validated): array
    {
        $out = [];
        foreach (CreatureMasteryColumns::all() as $column) {
            if (array_key_exists($column, $validated)) {
                $out[$column] = (int) $validated[$column];
            }
        }
        if (array_key_exists('intimidation_ability', $validated) && $validated['intimidation_ability'] !== null) {
            $out['intimidation_ability'] = (string) $validated['intimidation_ability'];
        }

        return $out;
    }

    private function resolveCreatureForMasteryValidation(): ?Creature
    {
        $creature = $this->route('creature');
        if ($creature instanceof Creature) {
            return $creature;
        }

        $monster = $this->route('monster');
        if ($monster instanceof Monster) {
            return $monster->creature;
        }

        $npc = $this->route('npc');
        if ($npc instanceof Npc) {
            return $npc->creature;
        }

        return null;
    }
}
