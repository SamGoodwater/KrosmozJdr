<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\ProjectScheduleTask;
use App\Models\User;
use Cron\CronExpression;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

/**
 * Mise à jour d’une entrée {@see ProjectScheduleTask} depuis l’admin super_user.
 *
 * @example
 * // $request après validation HTTP
 */
class UpdateProjectScheduleTaskRequest extends FormRequest
{
    /**
     * @return bool True si utilisateur interactif super_admin authentifié
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof User && $user->isInteractiveSuperAdmin();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'enabled' => ['sometimes', 'boolean'],
            'cron_expression' => ['sometimes', 'string', 'max:120'],
            'without_overlapping' => ['sometimes', 'boolean'],
        ];
    }

    /** @throws ValidationException Validation si expression cron invalide */
    protected function prepareForValidation(): void
    {
        if ($this->has('enabled')) {
            $this->merge([
                'enabled' => filter_var($this->input('enabled'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        if ($this->has('without_overlapping')) {
            $this->merge([
                'without_overlapping' => filter_var($this->input('without_overlapping'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function after(): array
    {
        return [
            fn (Validator $validator) => $this->validateCronWhenPresent($validator),
        ];
    }

    private function validateCronWhenPresent(Validator $validator): void
    {
        $expr = trim((string) $this->input('cron_expression', ''));
        if ($expr === '' || ! $this->has('cron_expression')) {
            return;
        }

        if (! CronExpression::isValidExpression($expr)) {
            $validator->errors()->add('cron_expression', 'Expression cron invalide (minute heure jour mois jour_sem).');
        }
    }
}
