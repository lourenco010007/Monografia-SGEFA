@extends('layouts.admin')

@section('title', 'Tarifas')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold">Tarifas</h1>
            <p class="mt-1 text-sm text-slate-600">Cada data de vigência identifica uma tarifa geral.</p>
        </div>
        <a class="rounded-lg bg-blue-700 px-4 py-2 text-center font-semibold text-white hover:bg-blue-800"
            href="{{ route('tarifas.create') }}">Registar tarifa</a>
    </div>

    <p class="mt-4 rounded-lg bg-blue-50 p-3 text-sm text-blue-900">
        As tarifas são versões por data de vigência. Para preservar o histórico, não podem ser apagadas; registe uma nova versão para alterações futuras.
    </p>

    <div class="mt-6 space-y-3">
        @forelse ($tarifas as $tarifa)
            <article class="rounded-xl border border-slate-200 p-4">
                <h2 class="font-semibold">{{ $tarifa->descricao }}</h2>
                <p class="text-sm text-slate-600">
                    {{ number_format((float) $tarifa->preco_m3, 2, ',', '.') }} por m³ · multa
                    {{ number_format((float) $tarifa->taxa_multa_pct, 2, ',', '.') }}% ·
                    {{ $tarifa->prazo_dias }} dias de prazo
                </p>
                <p class="text-sm text-slate-600">Vigente desde {{ $tarifa->vigente_desde->format('d/m/Y') }}</p>
            </article>
        @empty
            <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Ainda não existem tarifas.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $tarifas->links() }}</div>
@endsection
