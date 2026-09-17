<?php

namespace App\Http\Requests;

use App\Services\FileService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:'.implode(',', FileService::getAllowedUploadExtensions()),
                'max:'.FileService::MAX_SIZE,
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value instanceof UploadedFile && FileService::isSvgUpload($value)) {
                        $fail('Les fichiers SVG ne sont pas autorisés (contenu exécutable).');
                    }
                },
            ],
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
