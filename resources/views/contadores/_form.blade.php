<form class="mt-6 space-y-4" method="POST" action="{{ $action }}">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 p-3 text-sm text-red-800" role="alert">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label class="mb-1 block text-sm font-medium" for="numero_serie">Número de série</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="numero_serie" name="numero_serie"
            value="{{ old('numero_serie', $contador->numero_serie) }}" maxlength="255" required>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="utente_id">Utente</label>
        <select class="w-full rounded-lg border border-slate-300 px-3 py-2" id="utente_id" name="utente_id" required>
            <option value="">Seleccione um utente</option>
            @foreach ($utentes as $utente)
                <option value="{{ $utente->id }}"
                    @selected((string) old('utente_id', $contador->utente_id ?? '') === (string) $utente->id)>
                    {{ $utente->nome }} — {{ $utente->bairro }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="localizacao">Localização</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="localizacao" name="localizacao"
            value="{{ old('localizacao', $contador->localizacao) }}" maxlength="255" required>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="data_instalacao">Data de instalação</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="data_instalacao" name="data_instalacao"
            type="date" value="{{ old('data_instalacao', $contador->data_instalacao?->format('Y-m-d')) }}" required>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="estado">Estado</label>
        <select class="w-full rounded-lg border border-slate-300 px-3 py-2" id="estado" name="estado" required>
            <option value="activo" @selected(old('estado', $contador->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $contador->estado ?? 'activo') === 'inactivo')>Inactivo</option>
        </select>
    </div>
    <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800 sm:w-auto"
        type="submit">{{ $method === 'PUT' ? 'Guardar alterações' : 'Registar contador' }}</button>
</form>
