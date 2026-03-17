<?php

namespace App\Providers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('can-enter', function ($user = null, $permission) {
            if (!$user instanceof Authenticatable) {
                // Use the authenticated user if $user is not an instance of Authenticatable
                $user = auth()->user();
            }
            if ($user-> type === 1) {
                return true;
            }


                if ($user->can($permission)) {
                    return true;
                }

            return $user->hasPermission($permission);
        });
    }
}
