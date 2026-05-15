<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Http\Requests\BaseFormRequest;

class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDTO(): LoginDTO
    {
        return new LoginDTO(
            email:    $this->validated('email'),
            password: $this->validated('password'),
        );
    }
}
