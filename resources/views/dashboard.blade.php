<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel | Gestão de Água</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 px-4 py-10 text-slate-900">
    <main class="mx-auto w-full max-w-3xl rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold">Bem-vindo, {{ auth()->user()->nome }}</h1>
                <p class="mt-1 text-sm text-slate-600">Perfil: {{ auth()->user()->perfil }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg border border-slate-300 px-4 py-2 font-medium hover:bg-slate-50"
                    type="submit">Terminar sessão</button>
            </form>
        </div>
        @if (auth()->user()->isAdmin())
            <nav class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Gestão">
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('usuarios.index') }}">Utilizadores</a>
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('utentes.index') }}">Utentes</a>
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('contadores.index') }}">Contadores</a>
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('tarifas.index') }}">Tarifas</a>
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('leituras.index') }}">Leituras</a>
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('facturas.index') }}">Facturas</a>
            </nav>
        @elseif (auth()->user()->perfil === \App\Models\User::PROFILE_AGENT)
            <nav class="mt-8 grid gap-3 sm:grid-cols-2" aria-label="Operações do agente">
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('leituras.index') }}">Leituras</a>
                <a class="rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50" href="{{ route('facturas.index') }}">Facturas</a>
            </nav>
        @elseif (auth()->user()->perfil === \App\Models\User::PROFILE_TENANT)
            <nav class="mt-8" aria-label="Portal do utente">
                <a class="inline-block rounded-lg border border-slate-200 p-4 font-medium hover:bg-slate-50"
                    href="{{ route('facturas.index') }}">As minhas facturas</a>
            </nav>
        @endif
    </main>
</body>
</html>
