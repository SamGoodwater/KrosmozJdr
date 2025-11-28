<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * FormRequest pour la mise à jour d'un utilisateur.
 *
 * Valide les champs principaux du profil utilisateur, y compris l'avatar (image, max 5MB).
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Adapter selon ta logique d'autorisation
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'utilisateur.
     *
     * @return array<string, mixed> Règles de validation Laravel
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore(optional($this->route('user'))?->id ?? Auth::id()),
            ],
            'notifications_enabled' => ['sometimes', 'boolean'],
            'notification_channels' => ['sometimes', 'array'],
            'notification_channels.*' => ['sometimes', 'string', \App\Models\User::NOTIFICATION_CHANNELS],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'avatar' => ['sometimes', 'nullable', 'image', 'max:5120'], // 5MB max
        ];
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        if (isset($data['notifications_enabled'])) {
            $this->merge([
                'notifications_enabled' => filter_var($data['notifications_enabled'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if (!isset($data['notification_channels'])) {
            $this->merge([
                'notification_channels' => [],
            ]);
        }
        if (!isset($data['role'])) {
            $this->merge([
                'role' => User::ROLE_USER, // Utiliser la constante au lieu de l'array
            ]);
        }
    }
}
