<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Services\EntityDisplay\EntityDisplayVisibilityService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Valide la matrice « Gérer l’affichage » (rôle minimal par type d’entité × état).
 *
 * La liste blanche des clés et états est appliquée côté serveur dans {@see EntityDisplayVisibilityService::sanitizeStoredPayload()}.
 */
class UpdateEntityDisplayVisibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rules' => ['required', 'array'],
        ];
    }
}
