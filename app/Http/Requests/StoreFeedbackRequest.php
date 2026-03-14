<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour la soumission d'un retour utilisateur (bug, erreur, suggestion, autre).
 *
 * @see App\Http\Controllers\FeedbackController
 * @see docs/00-Project/FEEDBACK_SYSTEM.md
 */
class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string|max:2000',
            'type' => 'required|string|in:bug,error,suggestion,other',
            'url' => 'nullable|string|max:500',
            'pseudo' => 'nullable|string|max:100',
            'attachment' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,gif,pdf,txt',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Le message est requis.',
            'message.max' => 'Le message ne peut pas dépasser 2000 caractères.',
            'type.required' => 'Le type de retour est requis.',
            'type.in' => 'Le type choisi n\'est pas valide.',
            'attachment.max' => 'La pièce jointe ne peut pas dépasser 2 Mo.',
            'attachment.mimes' => 'La pièce jointe doit être une image (jpg, png, gif), un PDF ou un fichier texte.',
        ];
    }
}
