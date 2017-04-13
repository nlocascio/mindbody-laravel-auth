<?php

namespace Nlocascio\MindbodyAuth\Tests;

use Illuminate\Foundation\Auth\User;

trait CreatesUsers
{
    private function createUser($attributes = [])
    {
        ($user = new User())
            ->forceFill(array_merge([
                'name'     => 'Test User',
                'email'    => 'test@test.com',
                'password' => ''
            ], $attributes))->save();

        return $user;
    }
}