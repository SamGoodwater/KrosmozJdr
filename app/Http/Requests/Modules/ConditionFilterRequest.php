<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;

class ConditionFilterRequest extends FormRequest
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
            'is_unbewitchable' => ["boolean"],
            'is_malus' => ["boolean"],
            'usable' => ["boolean"],
            "name" => ["string", "min:4", "max:255", "required"],
            "description" => ["string", "min:4", "max:1000", "nullable"],
            "is_visible" => ["boolean"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("conditions", "uniqid")->ignore($this->route()->parameter('condition'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
            "image" => FileRules::rules([FileRules::TYPE_IMAGE]),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "is_visible" => $this->input("is_visible") ?: true,
            'created_by' => Auth::user()->id,
            'is_unbewitchable' => $this->input("is_unbewitchable") ?: false,
            'is_malus' => $this->input("is_malus") ?: false,
            'usable' => $this->input("usable") ?: true,
        ]);
    }
}
