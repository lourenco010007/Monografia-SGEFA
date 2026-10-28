<?php

namespace App\Http\Requests;

use App\Models\Contador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Contador::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'utente_id' => ['required', 'integer', Rule::exists('utentes', 'id')],
            'numero_serie' => ['required', 'string', 'max:255', Rule::unique('contadores', 'numero_serie')],
            'localizacao' => ['required', 'string', 'max:255'],
            'data_instalacao' => ['required', 'date'],
            'estado' => ['required', Rule::in([Contador::STATE_ACTIVE, Contador::STATE_INACTIVE])],
        ];
    }
}
