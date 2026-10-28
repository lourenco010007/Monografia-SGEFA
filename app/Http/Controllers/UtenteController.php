<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUtenteRequest;
use App\Http\Requests\UpdateUtenteRequest;
use App\Models\User;
use App\Models\Utente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UtenteController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Utente::class);

        return view('utentes.index', [
            'utentes' => Utente::query()->with('utilizador')->orderBy('nome')->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Utente::class);

        return view('utentes.create', [
            'utente' => new Utente,
            'utilizadores' => $this->availablePortalUsers(),
        ]);
    }

    public function store(StoreUtenteRequest $request): RedirectResponse
    {
        Utente::query()->create($request->validated());

        return to_route('utentes.index')->with('status', 'Utente criado com sucesso.');
    }

    public function edit(Utente $utente): View
    {
        Gate::authorize('view', $utente);

        return view('utentes.edit', [
            'utente' => $utente,
            'utilizadores' => $this->availablePortalUsers($utente->utilizador_id),
        ]);
    }

    public function update(UpdateUtenteRequest $request, Utente $utente): RedirectResponse
    {
        $utente->update($request->validated());

        return to_route('utentes.index')->with('status', 'Utente actualizado com sucesso.');
    }

    /**
     * @return Collection<int, User>
     */
    private function availablePortalUsers(?int $selectedUserId = null): Collection
    {
        return User::query()
            ->where('perfil', User::PROFILE_TENANT)
            ->where(function ($query) use ($selectedUserId): void {
                $query->where('activo', true)->whereDoesntHave('utente');

                if ($selectedUserId !== null) {
                    $query->orWhereKey($selectedUserId);
                }
            })
            ->orderBy('nome')
            ->get();
    }
}
