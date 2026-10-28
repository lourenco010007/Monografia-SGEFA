<?php

namespace App\Http\Requests;

use App\Models\Contador;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeituraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->perfil, [
            User::PROFILE_ADMIN,
            User::PROFILE_AGENT,
        ], true);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'contador_id' => [
                'required',
                'integer',
                Rule::exists('contadores', 'id')->where('estado', Contador::STATE_ACTIVE),
            ],
            'leitura_actual' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.999',
                'decimal:0,3',
            ],
            'data_leitura' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ];
    }
}
