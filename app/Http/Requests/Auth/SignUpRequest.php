<?php

namespace App\Http\Requests\Auth;

use App\Enums\Role;
use App\Rules\Documents\Cnpj;
use App\Rules\Documents\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class SignUpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'document' => ['required', Rule::unique('users'), $this->documentValidationRule()],
            'email' => ['required', 'string', 'email', Rule::unique('users')],
            'role' => ['required', new Enum(Role::class)],
            'password' => Password::required(),
        ];
    }

    private function documentValidationRule()
    {
        return match (Role::tryFrom($this->role)) {
            Role::Person => new Cpf,
            Role::Store => new Cnpj,
            default => null,
        };
    }
}
