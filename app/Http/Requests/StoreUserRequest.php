<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('utilizadores', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'perfil' => ['required', Rule::in([
                User::PROFILE_ADMIN,
                User::PROFILE_AGENT,
                User::PROFILE_TENANT,
            ])],
            'activo' => ['required', 'boolean'],
        ];
    }
}
