<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Utente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUtenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $utente = $this->route('utente');

        return $utente instanceof Utente && ($this->user()?->can('update', $utente) ?? false);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $utente = $this->route('utente');

        return [
            'utilizador_id' => [
                'nullable',
                Rule::unique('utentes', 'utilizador_id')->ignore($utente->getKey()),
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
