<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\FileService;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:' . implode(',', FileService::getAllowedExtensions()) . '|max:' . FileService::MAX_SIZE,
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
