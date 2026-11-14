<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Pagination compatible avec les vues Laravel 13 installées dans le framework.
        Paginator::useBootstrapFive();

        Gate::define('voir-finances', fn (User $u) => $u->aRole('admin', 'responsable'));
        Gate::define('gerer-utilisateurs', fn (User $u) => $u->aRole('admin'));
    }
}
