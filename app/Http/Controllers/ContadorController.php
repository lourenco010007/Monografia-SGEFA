<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContadorRequest;
use App\Http\Requests\UpdateContadorRequest;
use App\Models\Contador;
use App\Models\Utente;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContadorController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Contador::class);

        return view('contadores.index', [
            'contadores' => Contador::query()->with('utente')->orderBy('numero_serie')->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Contador::class);

        return view('contadores.create', [
            'contador' => new Contador,
            'utentes' => $this->activeUtentes(),
        ]);
    }

    public function store(StoreContadorRequest $request): RedirectResponse
    {
        Contador::query()->create($request->validated());

        return to_route('contadores.index')->with('status', 'Contador criado com sucesso.');
    }

    public function edit(Contador $contador): View
    {
        Gate::authorize('view', $contador);

        return view('contadores.edit', [
            'contador' => $contador,
            'utentes' => $this->activeUtentes($contador->utente_id),
        ]);
    }

    public function update(UpdateContadorRequest $request, Contador $contador): RedirectResponse
    {
        $dados = $request->validated();

        DB::transaction(function () use ($contador, $dados): void {
            $contador = Contador::query()
                ->whereKey($contador->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (
                (int) $dados['utente_id'] !== $contador->utente_id
                && $contador->leituras()->exists()
            ) {
                throw ValidationException::withMessages([
                    'utente_id' => 'Não é possível transferir um contador depois de existirem leituras; registe outro contador.',
                ]);
            }

            $ultimaLeitura = $contador->leituras()->orderByDesc('data_leitura')->first();

            if (
                $ultimaLeitura
                && $dados['data_instalacao'] > $ultimaLeitura->data_leitura->toDateString()
            ) {
                throw ValidationException::withMessages([
                    'data_instalacao' => 'A instalação não pode ser posterior a uma leitura já registada.',
                ]);
            }

            $contador->update($dados);
        });

        return to_route('contadores.index')->with('status', 'Contador actualizado com sucesso.');
    }

    /**
     * @return Collection<int, Utente>
     */
    private function activeUtentes(?int $selectedUtenteId = null): Collection
    {
        return Utente::query()
            ->where(function ($query) use ($selectedUtenteId): void {
                $query->where('estado', Utente::STATE_ACTIVE);

                if ($selectedUtenteId !== null) {
                    $query->orWhereKey($selectedUtenteId);
                }
            })
            ->orderBy('nome')
            ->get();
    }
}
