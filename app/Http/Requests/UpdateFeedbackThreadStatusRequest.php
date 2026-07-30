<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation d'un changement de statut de conversation feedback.
 *
 * @example
 * $status = $request->validated('status');
 */
class UpdateFeedbackThreadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:open,awaiting_user,closed',
        ];
    }
}
