<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * FormRequest pour la mise à jour d'un utilisateur.
 *
 * Valide : name, email, avatar (image max 5MB), notifications_enabled, notification_channels,
 * notification_preferences (par type : channels = ['database','mail'], frequency = instant|daily|weekly|monthly).
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user') ?? Auth::user();
        if (! $target instanceof User) {
            return false;
        }

        return (bool) $this->user()?->can('update', $target);
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
                Rule::unique('users', 'email')->ignore(optional($this->route('user'))?->id ?? Auth::id()),
            ],
            'notifications_enabled' => ['sometimes', 'boolean'],
            'notification_channels' => ['sometimes', 'array'],
            'notification_channels.*' => ['sometimes', 'string', Rule::in(['database', 'mail'])],
            'notification_preferences' => ['sometimes', 'nullable', 'array'],
            'notification_preferences.*' => ['sometimes', 'array'],
            'notification_preferences.*.channels' => ['sometimes', 'array'],
            'notification_preferences.*.channels.*' => ['sometimes', 'string', Rule::in(['database', 'mail'])],
            'notification_preferences.*.frequency' => ['sometimes', 'string', Rule::in(['instant', 'daily', 'weekly', 'monthly'])],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'avatar' => ['sometimes', 'nullable', 'image', 'max:5120'], // 5MB max
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('notifications_enabled')) {
            $raw = $this->input('notifications_enabled');
            if (is_bool($raw)) {
                return;
            }
            $this->merge([
                'notifications_enabled' => filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }
}
