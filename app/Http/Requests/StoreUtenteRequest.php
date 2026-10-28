<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Utente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUtenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Utente::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'utilizador_id' => [
                'nullable',
                Rule::unique('utentes', 'utilizador_id'),
                Rule::exists('utilizadores', 'id')
                    ->where('perfil', User::PROFILE_TENANT),
            ],
            'nome' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:50'],
            'bairro' => ['required', 'string', 'max:255'],
            'morada' => ['required', 'string', 'max:2000'],
            'estado' => ['required', Rule::in([Utente::STATE_ACTIVE, Utente::STATE_INACTIVE])],
        ];
    }
}
