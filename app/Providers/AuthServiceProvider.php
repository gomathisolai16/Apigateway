<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
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
        if (! $this->app->routesAreCached()) {
            Passport::routes();
            // Passport::tokensExpireIn(now()->addSeconds(env('AUTH_TOKEN_EXPIRY_IN_SECOND', '1800')));
            // Passport::refreshTokensExpireIn(now()->addSeconds(env('AUTH_REFRESH_TOKEN_EXPIRY_IN_SECOND', '3600')));
        }
    }
}
