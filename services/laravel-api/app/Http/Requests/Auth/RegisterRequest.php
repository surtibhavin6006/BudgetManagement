<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\Http\Requests\BaseFormRequest;

class RegisterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'monthly_income' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function toDTO(): RegisterDTO
    {
        return new RegisterDTO(
            name:          $this->validated('name'),
            email:         $this->validated('email'),
            password:      $this->validated('password'),
            monthlyIncome: (float) $this->validated('monthly_income'),
        );
    }
}
