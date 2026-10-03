<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\LoadingTip;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoadingTipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', LoadingTip::class) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:200'],
            'url' => ['nullable', 'string', 'max:2048', 'url:http,https'],
            'featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
            'url' => $this->filled('url') ? $this->string('url')->toString() : null,
        ]);
    }
}
