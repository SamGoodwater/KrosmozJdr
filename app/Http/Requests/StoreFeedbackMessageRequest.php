<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation d'une réponse dans une conversation de feedback.
 *
 * @example
 * $validated = $request->validated();
 */
class StoreFeedbackMessageRequest extends FormRequest
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
            'message' => 'required|string|max:2000',
            'attachment' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,gif,pdf,txt',
            ],
            'status' => 'nullable|string|in:open,awaiting_user,closed',
        ];
    }
}
