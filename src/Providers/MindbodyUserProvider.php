<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Auth;
use Illuminate\Auth\EloquentUserProvider;
use Nlocascio\MindbodyAuth\Contracts\MindbodyValidator as ValidatorContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class MindbodyUserProvider extends EloquentUserProvider
{
    private $validator;

    /**
     * MindbodyStaffUserProvider constructor.
     * @param ValidatorContract $validator
     * @param array $config
     */
    public function __construct(ValidatorContract $validator, array $config)
    {
        $this->model = $config['model'];
        $this->validator = $validator;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param UserContract $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        //
        // TODO: Make Username key configurable
        //
        $username = $credentials['email'] ?? $credentials['username'];
        $password = $credentials['password'];

        return $this->validator->check($username, $password);
    }
}
