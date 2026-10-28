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
        <label class="mb-1 block text-sm font-medium" for="nome">Nome</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="nome" name="nome"
            value="{{ old('nome', $utente->nome) }}" maxlength="255" required>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="telefone">Telefone</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="telefone" name="telefone"
            value="{{ old('telefone', $utente->telefone) }}" maxlength="50" autocomplete="tel" required>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="bairro">Bairro</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="bairro" name="bairro"
            value="{{ old('bairro', $utente->bairro) }}" maxlength="255" required>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="morada">Morada</label>
        <textarea class="w-full rounded-lg border border-slate-300 px-3 py-2" id="morada" name="morada"
            rows="3" maxlength="2000" required>{{ old('morada', $utente->morada) }}</textarea>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="utilizador_id">Conta do portal (opcional)</label>
        <select class="w-full rounded-lg border border-slate-300 px-3 py-2" id="utilizador_id" name="utilizador_id">
            <option value="">Sem conta associada</option>
            @foreach ($utilizadores as $utilizador)
                <option value="{{ $utilizador->id }}"
                    @selected((string) old('utilizador_id', $utente->utilizador_id ?? '') === (string) $utilizador->id)>
                    {{ $utilizador->nome }} — {{ $utilizador->email }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium" for="estado">Estado</label>
        <select class="w-full rounded-lg border border-slate-300 px-3 py-2" id="estado" name="estado" required>
            <option value="activo" @selected(old('estado', $utente->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $utente->estado ?? 'activo') === 'inactivo')>Inactivo</option>
        </select>
    </div>
    <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800 sm:w-auto"
        type="submit">{{ $method === 'PUT' ? 'Guardar alterações' : 'Registar utente' }}</button>
</form>
