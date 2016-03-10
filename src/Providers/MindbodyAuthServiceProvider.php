<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Auth;
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
        $model = config('mindbody-laravel-auth.model');

        Auth::provider('mindbody', function($app, array $config) use ($model) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new MindbodyUserProvider(new $model());
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/mindbody-laravel-auth.php', 'mindbody-laravel-auth'
        );
    }
}