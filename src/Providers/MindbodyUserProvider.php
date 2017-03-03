<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Str;
use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;
use Nlocascio\MindbodyAuth\Contracts\MindbodyValidator;

class MindbodyUserProvider implements UserProvider
{
    private $model;
    private $validator;

    /**
     * MindbodyStaffUserProvider constructor.
     * @param MindbodyValidator $validator
     * @param array $config
     */
    public function __construct(MindbodyValidator $validator, array $config)
    {
        $this->model = $config['model'];
        $this->validator = $validator;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if ( ! Str::contains($key, ['password'])) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     * @throws MindbodyErrorException
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $username = $credentials['email'] ?? $credentials['username'];
        $password = $credentials['password'];

        return $this->validator->check($username, $password);
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }

}
