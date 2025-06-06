<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\FileProcessionService;

class UpdateFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $extensions = array_merge(
            FileProcessionService::EXTENSIONS_IMAGE,
            FileProcessionService::EXTENSIONS_VIDEO,
            FileProcessionService::EXTENSIONS_AUDIO,
            FileProcessionService::EXTENSIONS_DOCUMENT
        );
        $maxSize = FileProcessionService::MAX_SIZE;

        return [
            'file' => 'sometimes|file|mimes:' . implode(',', $extensions) . '|max:' . $maxSize,
            'title' => 'sometimes|nullable|string|max:255',
            'comment' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'order' => 'sometimes|integer|min:0',
        ];
    }
}
