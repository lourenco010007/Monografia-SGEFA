<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        return view('usuarios.index', [
            'usuarios' => User::query()->orderBy('nome')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('usuarios.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());

        return to_route('usuarios.index')->with('status', 'Utilizador criado com sucesso.');
    }

    public function updateStatus(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        $isBeingDeactivated = ! $request->boolean('activo');

        if ($isBeingDeactivated && $user->is($request->user())) {
            return back()->withErrors(['activo' => 'Não pode desactivar a sua própria conta.']);
        }

        if (
            $isBeingDeactivated
            && $user->isAdmin()
            && ! User::query()
                ->where('perfil', User::PROFILE_ADMIN)
                ->where('activo', true)
                ->where('id', '!=', $user->getKey())
                ->exists()
        ) {
            return back()->withErrors(['activo' => 'Mantenha pelo menos um administrador activo.']);
        }

        $user->activo = ! $isBeingDeactivated;
        $user->save();

        return to_route('usuarios.index')->with('status', 'Estado do utilizador actualizado.');
    }
}
