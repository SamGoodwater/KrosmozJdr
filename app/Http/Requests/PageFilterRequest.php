<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PageFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'slug' => ['string', 'max:255', 'unique:pages,slug,' . $this->route('page')?->id],
            'is_public' => ['boolean'],
            'sections' => ['array'],
            'sections.*.title' => ['string', 'max:255'],
            'sections.*.content' => ['nullable', 'string'],
            'sections.*.order_num' => ['integer'],
            "keyword" => ["string", "nullable"],
            "order_num" => ["integer", "min:0"],
            "is_dropdown" => ["boolean"],
            "is_visible" => ["boolean"],
            "is_editable" => ["boolean"],
            "page_id" => ["integer", "exists:pages,uniqid", "nullable"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("pages", "uniqid")->ignore($this->route()->parameter('page'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
            'image' => ["string", "nullable"],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "slug" => $this->input("slug") ?: \Illuminate\Support\Str::slug($this->input("name")),
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "order_num" => $this->input("order_num") ?: 0,
            "is_dropdown" => $this->input("is_dropdown") ?: false,
            "is_public" => $this->input("is_public") ?: false,
            "is_visible" => $this->input("is_visible") ?: false,
            "is_editable" => $this->input("is_editable") ?: true,
            "page_id" => $this->input("page_id") ?: 0,
            'created_by' => Auth::user()->id ?? null,
        ]);
    }
}
