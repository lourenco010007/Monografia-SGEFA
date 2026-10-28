<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Contador;
use App\Models\Factura;
use App\Models\Leitura;
use App\Models\Tarifa;
use App\Models\Utente;
use App\Policies\ContadorPolicy;
use App\Policies\FacturaPolicy;
use App\Policies\LeituraPolicy;
use App\Policies\TarifaPolicy;
use App\Policies\UtentePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Utente::class, UtentePolicy::class);
        Gate::policy(Contador::class, ContadorPolicy::class);
        Gate::policy(Tarifa::class, TarifaPolicy::class);
        Gate::policy(Leitura::class, LeituraPolicy::class);
        Gate::policy(Factura::class, FacturaPolicy::class);
    }
}
