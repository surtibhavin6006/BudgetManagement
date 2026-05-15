<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesDTO;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    use ResolvesDTO;

    public function authorize(): bool
    {
        return true;
    }

    abstract public function toDTO(): object;
}
