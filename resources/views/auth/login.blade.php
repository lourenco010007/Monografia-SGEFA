<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sessão | Gestão de Água</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 px-4 py-10 text-slate-900">
    <main class="mx-auto w-full max-w-md rounded-2xl bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold">Gestão de Água</h1>
        <p class="mt-2 text-sm text-slate-600">Inicie sessão para consultar as funcionalidades do seu perfil.</p>

        <form class="mt-6 space-y-5" method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium" for="email">Email</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="email" name="email"
                    type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                @error('email')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium" for="password">Palavra-passe</label>
                <input class="w-full rounded-lg border border-slate-300 px-3 py-2" id="password" name="password"
                    type="password" autocomplete="current-password" required>
                @error('password')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800"
                type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>
