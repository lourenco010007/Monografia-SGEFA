@extends('layouts.app')

@section('title', 'Factura '.$factura->numero)

@section('content')
    <a class="text-sm text-blue-700 underline" href="{{ route('facturas.index') }}">Voltar às facturas</a>
    <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h1 class="text-2xl font-bold">Factura {{ $factura->numero }}</h1>
            <p class="mt-1 text-slate-600">{{ $factura->utente->nome }}</p>
        </div>
        <p class="inline-flex w-fit rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-800">
            {{ ucfirst($factura->estado) }}
        </p>
    </div>

    <dl class="mt-6 grid gap-4 rounded-xl border border-slate-200 p-4 sm:grid-cols-2">
        <div>
            <dt class="text-sm text-slate-600">Data de emissão</dt>
            <dd class="font-medium">{{ $factura->data_emissao->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-600">Data de vencimento</dt>
            <dd class="font-medium">{{ $factura->data_vencimento->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-600">Contador</dt>
            <dd class="font-medium">{{ $factura->leitura->contador->numero_serie }}</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-600">Data da leitura</dt>
            <dd class="font-medium">{{ $factura->leitura->data_leitura->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-600">Leitura anterior</dt>
            <dd class="font-medium">{{ number_format((float) $factura->leitura->leitura_anterior, 3, ',', '.') }} m³</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-600">Leitura actual</dt>
            <dd class="font-medium">{{ number_format((float) $factura->leitura->leitura_actual, 3, ',', '.') }} m³</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-600">Consumo</dt>
            <dd class="font-medium">{{ number_format((float) $factura->leitura->consumo_m3, 3, ',', '.') }} m³</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-600">Tarifa</dt>
            <dd class="font-medium">{{ $factura->tarifa->descricao }} · {{ number_format((float) $factura->tarifa->preco_m3, 2, ',', '.') }} por m³</dd>
        </div>
        <div class="border-t border-slate-200 pt-4 sm:col-span-2">
            <dt class="text-sm text-slate-600">Total</dt>
            <dd class="text-2xl font-bold">{{ number_format((float) $factura->valor_total, 2, ',', '.') }}</dd>
        </div>
    </dl>
@endsection
