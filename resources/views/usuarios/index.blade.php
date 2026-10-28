<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utilizadores | Gestão de Água</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 px-4 py-8 text-slate-900 sm:py-10">
    <main class="mx-auto w-full max-w-4xl rounded-2xl bg-white p-5 shadow-sm sm:p-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold">Utilizadores</h1>
                <a class="mt-2 inline-block text-sm text-blue-700 underline" href="{{ route('dashboard') }}">Voltar ao painel</a>
            </div>
            <a class="rounded-lg bg-blue-700 px-4 py-2 text-center font-semibold text-white hover:bg-blue-800"
                href="{{ route('usuarios.create') }}">Criar utilizador</a>
        </div>

        @if (session('status'))
            <p class="mt-5 rounded-lg bg-green-50 p-3 text-sm text-green-800">{{ session('status') }}</p>
        @endif
        @if ($errors->has('activo'))
            <p class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-800">{{ $errors->first('activo') }}</p>
        @endif

        <div class="mt-6 space-y-3">
            @forelse ($usuarios as $usuario)
                <article class="flex flex-col gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold">{{ $usuario->nome }}</h2>
                        <p class="text-sm text-slate-600">{{ $usuario->email }} · {{ $usuario->perfil }}</p>
                        <p class="text-sm {{ $usuario->activo ? 'text-green-700' : 'text-slate-500' }}">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </p>
                    </div>
                    @if (! $usuario->is(auth()->user()))
                        <form method="POST" action="{{ route('usuarios.status', $usuario) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="activo" value="{{ $usuario->activo ? '0' : '1' }}">
                            <button class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium hover:bg-slate-50 sm:w-auto"
                                type="submit">
                                {{ $usuario->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    @endif
                </article>
            @empty
                <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Ainda não existem utilizadores.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $usuarios->links() }}</div>
    </main>
</body>
</html>
