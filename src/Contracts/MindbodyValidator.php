<?php

namespace Nlocascio\MindbodyAuth\Contracts;

interface MindbodyValidator
{
    /**
     * Check the given username against the plain password.
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public function check($username, $password);
}