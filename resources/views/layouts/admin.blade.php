<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Gestão de Água</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 px-4 py-8 text-slate-900 sm:py-10">
    <main class="mx-auto w-full max-w-5xl rounded-2xl bg-white p-5 shadow-sm sm:p-8">
        <nav class="mb-6 flex flex-wrap gap-4 border-b border-slate-200 pb-4 text-sm" aria-label="Navegação de administração">
            <a class="text-blue-700 underline" href="{{ route('dashboard') }}">Painel</a>
            <a class="text-blue-700 underline" href="{{ route('utentes.index') }}">Utentes</a>
            <a class="text-blue-700 underline" href="{{ route('contadores.index') }}">Contadores</a>
            <a class="text-blue-700 underline" href="{{ route('tarifas.index') }}">Tarifas</a>
        </nav>

        @if (session('status'))
            <p class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-800">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>
