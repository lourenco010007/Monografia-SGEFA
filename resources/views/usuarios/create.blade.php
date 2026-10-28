<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar utilizador | Gestão de Água</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 px-4 py-8 text-slate-900 sm:py-10">
    <main class="mx-auto w-full max-w-xl rounded-2xl bg-white p-5 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold">Criar utilizador</h1>
        <a class="mt-2 inline-block text-sm text-blue-700 underline" href="{{ route('usuarios.index') }}">Voltar à lista</a>

        @if ($errors->any())
            <div class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-800" role="alert">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-6 space-y-4" method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium" for="nome">Nome</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="nome" name="nome"
                    value="{{ old('nome') }}" maxlength="255" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="email">Email</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="email" name="email"
                    type="email" value="{{ old('email') }}" maxlength="255" autocomplete="username" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="perfil">Perfil</label>
                <select class="w-full rounded-lg border border-slate-300 px-3 py-2" id="perfil" name="perfil" required>
                    <option value="admin" @selected(old('perfil') === 'admin')>Administrador</option>
                    <option value="agente" @selected(old('perfil') === 'agente')>Agente</option>
                    <option value="utente" @selected(old('perfil', 'utente') === 'utente')>Utente</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="password">Palavra-passe</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="password" name="password"
                    type="password" minlength="8" autocomplete="new-password" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium" for="password_confirmation">Confirmar palavra-passe</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="password_confirmation"
                    name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input name="activo" type="hidden" value="0">
                <input name="activo" type="checkbox" value="1" @checked(old('activo', '1') === '1')>
                Conta activa
            </label>
            <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800"
                type="submit">Criar conta</button>
        </form>
    </main>
</body>
</html>
