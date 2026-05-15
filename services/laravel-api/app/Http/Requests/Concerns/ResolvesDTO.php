<?php

namespace App\Http\Requests\Concerns;

trait ResolvesDTO
{
    protected function passedValidation(): void
    {
        $dto = $this->toDTO();
        app()->instance(get_class($dto), $dto);
    }
}
