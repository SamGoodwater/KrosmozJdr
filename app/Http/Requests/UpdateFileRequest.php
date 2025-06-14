<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\FileService;

class UpdateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'sometimes|file|mimes:' . implode(',', FileService::getAllowedExtensions()) . '|max:' . FileService::MAX_SIZE,
            'title' => 'sometimes|nullable|string|max:255',
            'comment' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'order' => 'sometimes|integer|min:0',
        ];
    }
}
