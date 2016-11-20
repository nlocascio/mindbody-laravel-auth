<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Nlocascio\Mindbody\Services\MindbodyService;

class MindbodyAuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/mindbody_auth.php' => config_path('mindbody_auth.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/mindbody_auth.php', 'mindbody_auth'
        );

        Auth::provider('mindbody', function ($app, $config) {
            return new MindbodyUserProvider($app->make(MindbodyService::class), $config);
        });

//        Auth::provider('mindbody_client', function () use ($mindbodyService) {
//            return new MindbodyClientUserProvider(config('mindbody_auth.client.model'), $mindbodyService);
//        });
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