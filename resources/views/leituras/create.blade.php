@extends('layouts.app')

@section('title', 'Registar leitura')

@section('content')
    <h1 class="text-2xl font-bold">Registar leitura</h1>
    <p class="mt-1 text-sm text-slate-600">
        A leitura anterior e o consumo são calculados pelo sistema. A primeira leitura de um contador começa em zero.
        Ao guardar, é gerada automaticamente uma factura com a tarifa vigente.
    </p>

    @if ($errors->any())
        <div class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-800" role="alert">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($contadores->isEmpty())
        <p class="mt-6 rounded-lg bg-amber-50 p-4 text-sm text-amber-900">
            Não há contadores activos associados a utentes activos. Peça ao administrador para verificar os registos.
        </p>
    @else
        <form class="mt-6 max-w-xl space-y-4" method="POST" action="{{ route('leituras.store') }}">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium" for="contador_id">Contador</label>
                <select class="w-full rounded-lg border border-slate-300 px-3 py-2" id="contador_id" name="contador_id" required>
                    <option value="">Seleccione um contador</option>
                    @foreach ($contadores as $contador)
                        @php($ultimaLeitura = $contador->leituras->first())
                        <option value="{{ $contador->id }}" @selected((string) old('contador_id') === (string) $contador->id)>
                            {{ $contador->numero_serie }} — {{ $contador->utente->nome }} —
                            anterior {{ $ultimaLeitura ? number_format((float) $ultimaLeitura->leitura_actual, 3, ',', '.') : '0,000' }} m³
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="leitura_actual">Leitura actual (m³)</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="leitura_actual" name="leitura_actual"
                    type="number" min="0" max="999999999.999" step="0.001"
                    value="{{ old('leitura_actual') }}" inputmode="decimal" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="data_leitura">Data da leitura</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="data_leitura" name="data_leitura"
                    type="date" max="{{ today()->toDateString() }}" value="{{ old('data_leitura', today()->toDateString()) }}" required>
            </div>
            <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800 sm:w-auto"
                type="submit">Registar e gerar factura</button>
        </form>
    @endif
@endsection
