<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Enregistre le markdown d’un fichier du journal (frise, intro ou version).
 *
 * @example PUT `/changelog/sources/roadmap` avec `{ "markdown": "## 1.4 · Prochaine version\n\n…" }`.
 */
class UpdateChangelogMarkdownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'markdown' => ['present', 'string', 'max:80000'],
        ];
    }
}
