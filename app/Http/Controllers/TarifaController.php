<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTarifaRequest;
use App\Models\Tarifa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TarifaController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Tarifa::class);

        return view('tarifas.index', [
            'tarifas' => Tarifa::query()->orderByDesc('vigente_desde')->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Tarifa::class);

        return view('tarifas.create');
    }

    public function store(StoreTarifaRequest $request): RedirectResponse
    {
        Tarifa::query()->create($request->validated());

        return to_route('tarifas.index')->with('status', 'Tarifa registada com sucesso.');
    }
}
