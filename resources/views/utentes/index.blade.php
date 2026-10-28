@extends('layouts.admin')

@section('title', 'Utentes')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold">Utentes</h1>
            <p class="mt-1 text-sm text-slate-600">Dados dos utentes e ligação opcional ao portal.</p>
        </div>
        <a class="rounded-lg bg-blue-700 px-4 py-2 text-center font-semibold text-white hover:bg-blue-800"
            href="{{ route('utentes.create') }}">Registar utente</a>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($utentes as $utente)
            <article class="flex flex-col gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold">{{ $utente->nome }}</h2>
                    <p class="text-sm text-slate-600">{{ $utente->telefone }} · {{ $utente->bairro }}</p>
                    <p class="text-sm text-slate-600">
                        Portal: {{ $utente->utilizador?->email ?? 'não associado' }}
                    </p>
                    <p class="text-sm {{ $utente->estado === 'activo' ? 'text-green-700' : 'text-slate-500' }}">
                        {{ ucfirst($utente->estado) }}
                    </p>
                </div>
                <a class="rounded-lg border border-slate-300 px-3 py-2 text-center text-sm font-medium hover:bg-slate-50"
                    href="{{ route('utentes.edit', $utente) }}">Editar</a>
            </article>
        @empty
            <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Ainda não existem utentes.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $utentes->links() }}</div>
@endsection
