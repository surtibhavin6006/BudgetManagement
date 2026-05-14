<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\RegisterRequest;

readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public float  $monthlyIncome,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            name:          $request->validated('name'),
            email:         $request->validated('email'),
            password:      $request->validated('password'),
            monthlyIncome: (float) $request->validated('monthly_income'),
        );
    }
}
