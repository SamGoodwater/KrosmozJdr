<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourcetypeFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            "name" => ["string", "min:4", "max:255", "required"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("resourcetypes", "uniqid")->ignore($this->route()->parameter('resourcetype'))],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
        ]);
    }
}
