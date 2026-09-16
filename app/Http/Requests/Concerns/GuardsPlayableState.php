<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

/**
 * Refuse `state = playable` sans ability `publish` (création ou transition).
 *
 * @example
 * class UpdateSpellRequest extends FormRequest {
 *     use GuardsPlayableState;
 *     protected function playableModelClass(): string { return Spell::class; }
 * }
 */
trait GuardsPlayableState
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $this->rejectUnauthorizedPlayableState($validator);
        });
    }

    /**
     * Classe Eloquent ciblée (création, ou repli si la route n’a pas encore de modèle).
     *
     * @return class-string<Model>
     */
    abstract protected function playableModelClass(): string;

    protected function rejectUnauthorizedPlayableState(Validator $validator): void
    {
        $to = $this->input('state');
        if (! is_string($to) || $to === '') {
            return;
        }

        $model = $this->routeBoundEntity();
        $from = $model instanceof Model ? $model->getAttribute('state') : null;
        $from = is_string($from) ? $from : null;
        if (! EntityStateGate::requiresPublish($from, $to)) {
            return;
        }

        $user = $this->user();
        $target = $model ?? $this->playableModelClass();
        if ($user === null || ! $user->can('publish', $target)) {
            $validator->errors()->add(
                'state',
                'Passer une fiche en jouable exige le droit de publication (relecture).'
            );
        }
    }

    protected function routeBoundEntity(): ?Model
    {
        foreach ($this->route()?->parameters() ?? [] as $param) {
            if ($param instanceof Model) {
                return $param;
            }
        }

        return null;
    }
}
