<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\LoadingTip;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLoadingTipRequest extends FormRequest
{
    public function authorize(): bool
    {
        $loadingTip = $this->route('loading_tip');

        return is_object($loadingTip) && $this->user()?->can('update', $loadingTip) === true;
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
            'duration_seconds' => [
                'required',
                'integer',
                'min:'.LoadingTip::MIN_DURATION_SECONDS,
                'max:'.LoadingTip::MAX_DURATION_SECONDS,
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'is_active' => $this->boolean('is_active'),
            'url' => $this->filled('url') ? $this->string('url')->toString() : null,
            'duration_seconds' => $this->filled('duration_seconds')
                ? (int) $this->input('duration_seconds')
                : LoadingTip::DEFAULT_DURATION_SECONDS,
        ]);
    }
}
