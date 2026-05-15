<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\ForgotPasswordDTO;
use App\Http\Requests\BaseFormRequest;

class ForgotPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    public function toDTO(): ForgotPasswordDTO
    {
        return new ForgotPasswordDTO(
            email: $this->validated('email'),
        );
    }
}
