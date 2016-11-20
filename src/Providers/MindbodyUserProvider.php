<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;
use Nlocascio\Mindbody\Services\MindbodyService;

class MindbodyUserProvider implements UserProvider {

    public $model;
    private $mindbodyService;
    private $passwordField;
    private $usernameField;
    private $userType;

    /**
     * MindbodyStaffUserProvider constructor.
     * @param MindbodyService $mindbodyService
     * @param $config
     * @internal param $app
     */
    public function __construct(MindbodyService $mindbodyService, $config)
    {
        $this->model = $config['model'];
        $this->userType = $config['type'];

        $this->usernameField = $config['username_field'] ?? config('mindbody_auth.username_field');
        $this->passwordField = $config['password_field'] ?? config('mindbody_auth.password_field');

        $this->mindbodyService = $mindbodyService;
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
        if ($user != null)
        {
            $user->setRememberToken($token);

            $user->save();
        }
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

        $user = $this->createModel()->firstOrNew(
            array_filter($credentials, function ($key) {
                return ! Str::contains($key, 'password');
            }, ARRAY_FILTER_USE_KEY));

        return $user;
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
        try {
            $this->validateWithMindbody($user, [
                'Username' => $credentials[$this->usernameField],
                'Password' => $credentials[$this->passwordField],
            ]);

        } catch (MindbodyErrorException $e) {
            if ($e->getCode() == 104 || $e->getCode() == 315) {
                Log::notice('User failed to login.', ['username' => $credentials[$this->usernameField]]);

                return false;
            }

            throw $e;
        }

        return $user->save();
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * @param $user
     * @param $credentials
     */
    private function validateWithMindbody($user, $credentials)
    {
        if (method_exists($user, "getMindbodySiteId")) {
            $this->mindbodyService->setSiteId($user->getMindbodySiteId());
        }

        if ($this->userType == 'staff') {
            $result = $this->mindbodyService->ValidateStaffLogin($credentials)->Staff;
        }

        if ($this->userType == 'client') {
            $result = $this->mindbodyService->ValidateLogin($credentials)->Client;
        }

        $user->fillMindbodyData((array) $result);
    }



}
