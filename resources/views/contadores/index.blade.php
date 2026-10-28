@extends('layouts.admin')

@section('title', 'Contadores')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold">Contadores</h1>
            <p class="mt-1 text-sm text-slate-600">Cada contador fica associado a um utente.</p>
        </div>
        <a class="rounded-lg bg-blue-700 px-4 py-2 text-center font-semibold text-white hover:bg-blue-800"
            href="{{ route('contadores.create') }}">Registar contador</a>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($contadores as $contador)
            <article class="flex flex-col gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold">{{ $contador->numero_serie }}</h2>
                    <p class="text-sm text-slate-600">{{ $contador->utente->nome }} · {{ $contador->localizacao }}</p>
                    <p class="text-sm text-slate-600">Instalado em {{ $contador->data_instalacao->format('d/m/Y') }}</p>
                    <p class="text-sm {{ $contador->estado === 'activo' ? 'text-green-700' : 'text-slate-500' }}">
                        {{ ucfirst($contador->estado) }}
                    </p>
                </div>
                <a class="rounded-lg border border-slate-300 px-3 py-2 text-center text-sm font-medium hover:bg-slate-50"
                    href="{{ route('contadores.edit', $contador) }}">Editar</a>
            </article>
        @empty
            <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Ainda não existem contadores.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $contadores->links() }}</div>
@endsection
