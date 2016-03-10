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

        Auth::provider('mindbody-staff', function() use ($model) {
            return new MindbodyStaffUserProvider;
        });

        Auth::provider('mindbody-client', function() use ($model) {
            return new MindbodyClientUserProvider;
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