@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
    <h1 class="text-2xl font-bold">{{ auth()->user()->perfil === \App\Models\User::PROFILE_TENANT ? 'As minhas facturas' : 'Facturas' }}</h1>
    <p class="mt-1 text-sm text-slate-600">
        {{ auth()->user()->perfil === \App\Models\User::PROFILE_TENANT ? 'Facturas associadas à sua conta.' : 'Facturas geradas automaticamente a partir das leituras.' }}
    </p>

    <div class="mt-6 space-y-3">
        @forelse ($facturas as $factura)
            <a class="block rounded-xl border border-slate-200 p-4 hover:bg-slate-50"
                href="{{ route('facturas.show', $factura) }}">
                <div class="flex flex-col justify-between gap-2 sm:flex-row">
                    <div>
                        <h2 class="font-semibold">{{ $factura->numero }} · {{ $factura->utente->nome }}</h2>
                        <p class="text-sm text-slate-600">Emitida em {{ $factura->data_emissao->format('d/m/Y') }} · vence em {{ $factura->data_vencimento->format('d/m/Y') }}</p>
                    </div>
                    <div class="sm:text-right">
                        <p class="font-semibold">{{ number_format((float) $factura->valor_total, 2, ',', '.') }}</p>
                        <p class="text-sm text-slate-600">{{ ucfirst($factura->estado) }}</p>
                    </div>
                </div>
            </a>
        @empty
            <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Não existem facturas para apresentar.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $facturas->links() }}</div>
@endsection
