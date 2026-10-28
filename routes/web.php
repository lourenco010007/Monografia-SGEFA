<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContadorController;
use App\Http\Controllers\LeituraController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\UtenteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware(['auth', 'activo'])->group(function (): void {
    Route::get('/painel', fn () => view('dashboard'))->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/painel/admin', fn () => view('dashboard'))
        ->middleware('perfil:admin')
        ->name('dashboard.admin');
    Route::get('/painel/agente', fn () => view('dashboard'))
        ->middleware('perfil:agente')
        ->name('dashboard.agente');
    Route::get('/painel/utente', fn () => view('dashboard'))
        ->middleware('perfil:utente')
        ->name('dashboard.utente');

    Route::middleware('perfil:admin')->group(function (): void {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::patch('/usuarios/{user}/activo', [UsuarioController::class, 'updateStatus'])
            ->name('usuarios.status');

        Route::get('/utentes', [UtenteController::class, 'index'])->name('utentes.index');
        Route::get('/utentes/create', [UtenteController::class, 'create'])->name('utentes.create');
        Route::post('/utentes', [UtenteController::class, 'store'])->name('utentes.store');
        Route::get('/utentes/{utente}/edit', [UtenteController::class, 'edit'])->name('utentes.edit');
        Route::put('/utentes/{utente}', [UtenteController::class, 'update'])->name('utentes.update');

        Route::get('/contadores', [ContadorController::class, 'index'])->name('contadores.index');
        Route::get('/contadores/create', [ContadorController::class, 'create'])->name('contadores.create');
        Route::post('/contadores', [ContadorController::class, 'store'])->name('contadores.store');
        Route::get('/contadores/{contador}/edit', [ContadorController::class, 'edit'])->name('contadores.edit');
        Route::put('/contadores/{contador}', [ContadorController::class, 'update'])->name('contadores.update');

        Route::get('/tarifas', [TarifaController::class, 'index'])->name('tarifas.index');
        Route::get('/tarifas/create', [TarifaController::class, 'create'])->name('tarifas.create');
        Route::post('/tarifas', [TarifaController::class, 'store'])->name('tarifas.store');
    });

    Route::middleware('perfil:admin,agente')->group(function (): void {
        Route::get('/leituras', [LeituraController::class, 'index'])->name('leituras.index');
        Route::get('/leituras/create', [LeituraController::class, 'create'])->name('leituras.create');
        Route::post('/leituras', [LeituraController::class, 'store'])->name('leituras.store');
    });

    Route::middleware('perfil:admin,agente,utente')->group(function (): void {
        Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
        Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
    });

});
