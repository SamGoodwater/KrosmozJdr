<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Restauration complète depuis l’admin (confirmation du nom exact).
 */
class RestoreProjectBackupWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->isInteractiveSuperAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9._-]+$/'],
            'confirm_name' => ['required', 'string', 'max:255'],
            'no_safety_backup' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ((string) $this->input('name') !== (string) $this->input('confirm_name')) {
                $validator->errors()->add(
                    'confirm_name',
                    'Le nom saisi doit correspondre exactement à l’archive à restaurer.'
                );
            }
        });
    }
}
