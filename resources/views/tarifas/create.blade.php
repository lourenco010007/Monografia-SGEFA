@extends('layouts.admin')

@section('title', 'Registar tarifa')

@section('content')
    <h1 class="text-2xl font-bold">Registar tarifa</h1>
    <p class="mt-1 text-sm text-slate-600">Use uma data de vigência única; alterações futuras devem ser registadas como nova tarifa.</p>

    @if ($errors->any())
        <div class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-800" role="alert">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="mt-6 space-y-4" method="POST" action="{{ route('tarifas.store') }}">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium" for="descricao">Descrição</label>
            <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="descricao" name="descricao"
                value="{{ old('descricao') }}" maxlength="255" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium" for="preco_m3">Preço por m³</label>
            <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="preco_m3" name="preco_m3"
                type="number" step="0.01" min="0.01" value="{{ old('preco_m3') }}" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium" for="taxa_multa_pct">Taxa de multa (%)</label>
            <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="taxa_multa_pct" name="taxa_multa_pct"
                type="number" step="0.01" min="0" max="100" value="{{ old('taxa_multa_pct') }}" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium" for="prazo_dias">Prazo de pagamento (dias)</label>
            <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="prazo_dias" name="prazo_dias"
                type="number" step="1" min="0" max="65535" value="{{ old('prazo_dias') }}" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium" for="vigente_desde">Vigente desde</label>
            <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="vigente_desde" name="vigente_desde"
                type="date" value="{{ old('vigente_desde') }}" required>
        </div>
        <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800 sm:w-auto"
            type="submit">Registar tarifa</button>
    </form>
@endsection
