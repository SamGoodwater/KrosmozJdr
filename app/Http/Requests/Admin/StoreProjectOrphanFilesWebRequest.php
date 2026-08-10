<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation du lancement d’un nettoyage de fichiers Media orphelins depuis l’admin.
 */
class StoreProjectOrphanFilesWebRequest extends FormRequest
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
            'delete' => ['sometimes', 'boolean'],
            'skip_notify' => ['sometimes', 'boolean'],
        ];
    }
}
