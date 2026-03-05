<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

/**
 * FormRequest pour la création d'un utilisateur.
 *
 * Valide les champs principaux du profil utilisateur, y compris l'avatar (image, max 5MB).
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', User::class);
    }

    /**
     * Règles de validation pour la création d'utilisateur.
     *
     * @return array<string, mixed> Règles de validation Laravel
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'role' => ['required', 'integer', Rule::in([
                User::ROLE_GUEST,
                User::ROLE_USER,
                User::ROLE_PLAYER,
                User::ROLE_GAME_MASTER,
                User::ROLE_ADMIN,
            ])],
            'notifications_enabled' => ['sometimes', 'boolean'],
            'notification_channels' => ['sometimes', 'array'],
            'notification_channels.*' => ['sometimes', 'string', Rule::in(['database', 'mail'])],
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
        if (!isset($data['role']) || !is_numeric($data['role'])) {
            $this->merge([
                'role' => User::ROLE_USER,
            ]);
        }
    }
}
