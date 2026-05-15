<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\ResetPasswordDTO;
use App\Http\Requests\BaseFormRequest;

class ResetPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function toDTO(): ResetPasswordDTO
    {
        return new ResetPasswordDTO(
            token:    $this->validated('token'),
            email:    $this->validated('email'),
            password: $this->validated('password'),
        );
    }
}
