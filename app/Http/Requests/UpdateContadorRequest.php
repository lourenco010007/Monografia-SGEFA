<?php

namespace App\Http\Requests;

use App\Models\Contador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $contador = $this->route('contador');

        return $contador instanceof Contador && ($this->user()?->can('update', $contador) ?? false);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $contador = $this->route('contador');

        return [
            'utente_id' => ['required', 'integer', Rule::exists('utentes', 'id')],
            'numero_serie' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contadores', 'numero_serie')->ignore($contador->getKey()),
            ],
            'localizacao' => ['required', 'string', 'max:255'],
            'data_instalacao' => ['required', 'date'],
            'estado' => ['required', Rule::in([Contador::STATE_ACTIVE, Contador::STATE_INACTIVE])],
        ];
    }
}
