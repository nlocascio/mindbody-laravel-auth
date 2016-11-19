<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class MindbodyAuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/mindbody-laravel-auth.php' => config_path('mindbody-laravel-auth.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/mindbody-laravel-auth.php', 'mindbody_auth'
        );

        Auth::provider('mindbody_staff', function() {
            return new MindbodyStaffUserProvider(config('mindbody_auth.staff_model'));
        });

        Auth::provider('mindbody_client', function() {
            return new MindbodyClientUserProvider(config('mindbody_auth.client_model'));
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}