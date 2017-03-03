<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Nlocascio\MindbodyAuth\Services\MindbodyClientValidator;
use Nlocascio\MindbodyAuth\Services\MindbodyStaffValidator;

class MindbodyAuthServiceProvider extends ServiceProvider
{
//    protected $defer = true;

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/mindbody_auth.php' => config_path('mindbody_auth.php'),
        ]);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/mindbody_auth.php', 'mindbody_auth'
        );

        Auth::provider('mindbody_staff', function ($app, $config) {
            return new MindbodyUserProvider($app->make(MindbodyStaffValidator::class), $config);
        });

        Auth::provider('mindbody_client', function ($app, $config) {
            return new MindbodyUserProvider($app->make(MindbodyClientValidator::class), $config);
        });
    }
}