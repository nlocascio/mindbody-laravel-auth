<?php

namespace Nlocascio\MindbodyAuth\Contracts;

/**
 * Created by PhpStorm.
 * User: nlocascio
 * Date: 3/2/17
 * Time: 11:33 AM
 */
interface MindbodyValidator
{
    public function check($username, $password);
}