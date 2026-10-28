@extends('layouts.app')

@section('title', 'Leituras')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold">Leituras</h1>
            <p class="mt-1 text-sm text-slate-600">Histórico de leituras registadas; os registos não podem ser alterados nem apagados.</p>
        </div>
        <a class="rounded-lg bg-blue-700 px-4 py-2 text-center font-semibold text-white hover:bg-blue-800"
            href="{{ route('leituras.create') }}">Registar leitura</a>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($leituras as $leitura)
            <article class="rounded-xl border border-slate-200 p-4">
                <div class="flex flex-col justify-between gap-2 sm:flex-row">
                    <div>
                        <h2 class="font-semibold">{{ $leitura->contador->numero_serie }} · {{ $leitura->contador->utente->nome }}</h2>
                        <p class="text-sm text-slate-600">
                            {{ $leitura->data_leitura->format('d/m/Y') }} · anterior
                            {{ number_format((float) $leitura->leitura_anterior, 3, ',', '.') }} m³ · actual
                            {{ number_format((float) $leitura->leitura_actual, 3, ',', '.') }} m³
                        </p>
                        <p class="text-sm text-slate-600">
                            Consumo {{ number_format((float) $leitura->consumo_m3, 3, ',', '.') }} m³ ·
                            registada por {{ $leitura->agente->nome }}
                        </p>
                    </div>
                    <a class="text-sm font-medium text-blue-700 underline"
                        href="{{ route('facturas.show', $leitura->factura) }}">Ver factura {{ $leitura->factura->numero }}</a>
                </div>
            </article>
        @empty
            <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Ainda não existem leituras registadas.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $leituras->links() }}</div>
@endsection
