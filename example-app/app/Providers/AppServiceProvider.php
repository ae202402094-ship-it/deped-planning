<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

   
    public function boot(): void
{
    // Super Admin can do everything
    Gate::before(function (User $user) {
        if ($user->isSuperAdmin()) return true;
    });

    // Admin can manage schools but NOT users
    Gate::define('manage-schools', function (User $user) {
        return $user->isAdmin() || $user->isSuperAdmin();
    });

    // Only Super Admin can manage user approvals
    Gate::define('manage-users', function (User $user) {
        return $user->isSuperAdmin();
    });
}
}
