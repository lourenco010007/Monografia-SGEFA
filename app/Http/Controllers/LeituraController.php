<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeituraRequest;
use App\Models\Contador;
use App\Models\Leitura;
use App\Services\RegistarLeitura;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LeituraController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Leitura::class);

        return view('leituras.index', [
            'leituras' => Leitura::query()
                ->with(['contador.utente', 'agente'])
                ->orderByDesc('data_leitura')
                ->orderByDesc('id')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Leitura::class);

        $contadores = Contador::query()
            ->where('estado', Contador::STATE_ACTIVE)
            ->whereHas('utente', fn ($query) => $query->where('estado', 'activo'))
            ->with(['utente', 'leituras' => fn ($query) => $query
                ->orderByDesc('data_leitura')
                ->orderByDesc('id')
                ->limit(1)])
            ->orderBy('numero_serie')
            ->get();

        return view('leituras.create', ['contadores' => $contadores]);
    }

    public function store(StoreLeituraRequest $request, RegistarLeitura $registarLeitura): RedirectResponse
    {
        $dados = $request->validated();
        $factura = $registarLeitura->execute(
            $request->user(),
            (int) $dados['contador_id'],
            (string) $dados['leitura_actual'],
            $dados['data_leitura'],
        );

        return to_route('facturas.show', $factura)
            ->with('status', 'Leitura registada e factura gerada automaticamente.');
    }
}
