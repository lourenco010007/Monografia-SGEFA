<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FacturaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Factura::class);

        $user = request()->user();
        $facturas = Factura::query()
            ->with(['utente', 'leitura.contador'])
            ->when(
                $user->perfil === User::PROFILE_TENANT,
                fn ($query) => $query->where('utente_id', $user->utente?->getKey()),
            )
            ->orderByDesc('data_emissao')
            ->orderByDesc('id')
            ->paginate(15);

        return view('facturas.index', ['facturas' => $facturas]);
    }

    public function show(Factura $factura): View
    {
        Gate::authorize('view', $factura);

        return view('facturas.show', [
            'factura' => $factura->load(['utente', 'tarifa', 'leitura.contador']),
        ]);
    }
}
