<?php

namespace Nlocascio\MindbodyAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Nlocascio\MindbodyAuth\Providers\MindbodyUserProvider;
use Nlocascio\MindbodyAuth\Services\MindbodyClientValidator;
use Nlocascio\MindbodyAuth\Services\MindbodyStaffValidator;

class MindbodyAuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        Auth::provider('mindbody_staff', function ($app, $config) {
            return new MindbodyUserProvider(
                $app->make(MindbodyStaffValidator::class), $config
            );
        });

        Auth::provider('mindbody_client', function ($app, $config) {
            return new MindbodyUserProvider(
                $app->make(MindbodyClientValidator::class), $config
            );
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