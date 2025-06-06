<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\FileProcessionService;

class StoreFileRequest extends FormRequest
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
            'file' => 'required|file|mimes:' . implode(',', $extensions) . '|max:' . $maxSize,
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
