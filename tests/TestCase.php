<?php

namespace Nlocascio\MindbodyAuth\Tests;

use Nlocascio\Mindbody\MindbodyServiceProvider;
use Nlocascio\MindbodyAuth\MindbodyAuthServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Dotenv\Dotenv;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        $this->loadEnvironmentVariables();

        parent::setUp();

        $this->loadLaravelMigrations('testbench');
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.mindbody_client', [
            'driver' => 'mindbody_client',
            'model' => \Illuminate\Foundation\Auth\User::class
        ]);

        $app['config']->set('auth.providers.mindbody_staff', [
            'driver' => 'mindbody_staff',
            'model' => \Illuminate\Foundation\Auth\User::class
        ]);

        $app['config']->set('auth.guards.mindbody_client', [
            'driver' => 'session',
            'provider' => 'mindbody_client'
        ]);

        $app['config']->set('auth.guards.mindbody_staff', [
            'driver' => 'session',
            'provider' => 'mindbody_staff'
        ]);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MindbodyServiceProvider::class,
            MindbodyAuthServiceProvider::class
        ];
    }

    /**
     *
     */
    private function loadEnvironmentVariables()
    {
        if (! file_exists(__DIR__ . '/../.env')) {
            return;
        }

        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();
        $dotenv->required(['MINDBODY_SITEIDS', 'MINDBODY_SOURCENAME', 'MINDBODY_SOURCEPASSWORD']);
    }
}