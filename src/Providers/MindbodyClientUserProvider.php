<?php

namespace Nlocascio\MindbodyAuth\Providers;

use Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Log;
use Nlocascio\Mindbody\Services\MindbodyService;

class MindbodyClientUserProvider implements UserProvider {

    public $model;
    protected $mindbodyApi;

    public function __construct()
    {
        $model = config('auth.providers.users.model');
        $this->model = new $model();
        $this->mindbodyApi = new MindbodyService;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $user = $this->model->where('id', $identifier)->first();

        return $user;
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
        return $this->model->where('id', $identifier)->where('remember_token', $token)->first();
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
        $user = null;

        $user = $this->model->firstOrNew(['email' => $credentials['email']]);

        Log::debug("retrieveByCredentials: " . json_encode($user) . ' ' . json_encode($credentials));

        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {

        Log::debug("validateCredentials: " . json_encode($user) . ' ' . json_encode($credentials));

        if ( ! $user->email == $credentials['email'])
        {
            Log::debug("validateCredentials: login failed at " . __LINE__);

            return false;
        }

        $validateLoginResult = $this->mindbodyApi->ValidateLogin([
            'Username' => $credentials['email'],
            'Password' => $credentials['password']
        ])->ValidateLoginResult;

        if ( ! isset ($validateLoginResult->ErrorCode) || $validateLoginResult->ErrorCode != 200)
        {
            Log::debug("validateCredentials: login failed at " . __LINE__);
            Log::debug("validateCredentials: " . json_encode($validateLoginResult));

            return false;
        }

        $client = $validateLoginResult->Client;

        $user->fill([
            'name' => isset($client->FirstName) && $client->LastName
                ? "$client->FirstName $client->LastName" : null
        ]);

        Log::debug("validateCredentials: login succeeded at " . __LINE__);

        return $user->save();

    }
}
