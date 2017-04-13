<?php

namespace Nlocascio\MindbodyAuth\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Nlocascio\Mindbody\Mindbody;
use Nlocascio\MindbodyAuth\Tests\TestCase as BaseTestCase;
use \Mockery as m;

class MindbodyAuthTest extends BaseTestCase
{
    use DatabaseMigrations, CreatesUsers;

    /**
     * Integration tests for the package. Some of these tests WILL hit the MINDBODY API when called.
     */

    /** @test */
    public function it_authenticates_a_mindbody_client_user()
    {
        $this->createUser();

        ($mindbody = m::mock(Mindbody::class))
            ->shouldReceive('ValidateLogin')
            ->with([
                'Username' => 'test@test.com',
                'Password' => 'secret'
            ])
            ->once()
            ->andReturn((object) ['ErrorCode' => 200]);

        $this->app->instance(Mindbody::class, $mindbody);

        $attemptLogin = Auth::guard('mindbody_client')->attempt([
            'email'    => 'test@test.com',
            'password' => 'secret'
        ]);

        $this->assertTrue($attemptLogin);
    }

    /** @test */
    public function it_authenticates_a_mindbody_staff_user()
    {
        $this->createUser();

        ($mindbody = m::mock(Mindbody::class))
            ->shouldReceive('ValidateStaffLogin')
            ->with([
                'Username' => 'test@test.com',
                'Password' => 'secret'
            ])
            ->once()
            ->andReturn((object) ['ErrorCode' => 200]);

        $this->app->instance(Mindbody::class, $mindbody);

        $attemptLogin = Auth::guard('mindbody_staff')->attempt([
            'email'    => 'test@test.com',
            'password' => 'secret'
        ]);

        $this->assertTrue($attemptLogin);
    }

    /** @test */
    public function it_fails_authentication_with_bad_password_for_a_mindbody_client_user()
    {
        $this->createUser();

        $attemptLogin = Auth::guard('mindbody_client')->attempt([
            'email'    => 'test@test.com',
            'password' => 'ThisIsNotGoingToWork'
        ]);

        $this->assertFalse($attemptLogin);
    }

    /** @test */
    public function it_fails_authentication_with_bad_password_for_a_mindbody_staff_user()
    {
        $this->createUser();

        $attemptLogin = Auth::guard('mindbody_staff')->attempt([
            'email'    => 'test@test.com',
            'password' => 'ThisIsNotGoingToWork'
        ]);

        $this->assertFalse($attemptLogin);
    }
}