<?php

namespace App\Http\Requests;

use App\Models\Tarifa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTarifaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Tarifa::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'descricao' => ['required', 'string', 'max:255'],
            'preco_m3' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'taxa_multa_pct' => ['required', 'numeric', 'between:0,100', 'decimal:0,2'],
            'prazo_dias' => ['required', 'integer', 'between:0,65535'],
            'vigente_desde' => [
                'required',
                'date',
                Rule::unique('tarifas', 'vigente_desde'),
            ],
        ];
    }
}
