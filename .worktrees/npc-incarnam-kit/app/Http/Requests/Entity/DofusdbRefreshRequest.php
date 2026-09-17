<?php

declare(strict_types=1);

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Options d’une mise à jour DofusDB unitaire (id local).
 *
 * @example
 * ['mode' => 'preview']
 * ['mode' => 'full', 'force' => true]
 */
class DofusdbRefreshRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isGameMaster() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'mode' => ['required', 'string', Rule::in(['preview', 'full', 'images_only'])],
            'force' => ['sometimes', 'boolean'],
        ];
    }

    public function mode(): string
    {
        return (string) $this->validated('mode');
    }

    public function force(): bool
    {
        return $this->boolean('force');
    }
}
