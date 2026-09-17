<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation des options pour lancer `project:clear` depuis l’admin (job file d’attente).
 */
class StoreProjectClearWebRequest extends FormRequest
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
            'mode' => ['required', 'string', Rule::in(['safe', 'all'])],
        ];
    }

    /**
     * @return array<string, bool>
     */
    public function artisanOptions(): array
    {
        if ($this->input('mode') === 'all') {
            return ['--all' => true];
        }

        return ['--safe' => true];
    }
}
