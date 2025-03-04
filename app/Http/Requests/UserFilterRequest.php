<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;
use App\Models\User;

class UserFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'name' => ["nullable", "string", "min:1", "max:255"],
            'email' => ["nullable", "string", "email", "min:1", "max:255"],
            'password' => ["nullable", "string", "min:1", "max:255"],
            'role' => ["nullable", "string", Rule::in(array_keys(User::ROLES))],
            'email_verified_at' => ["nullable", "date"],
            "uniqid" => ["nullable", "string", "min:1", "max:255", Rule::unique("users", "uniqid")->ignore($this->route()->parameter('user'))],
            'avatar' => ["nullable", FileRules::rules([FileRules::TYPE_IMAGE])], // 1MB max
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "role" => $this->input("role") ?: User::ROLES["user"],
        ]);
    }
}
