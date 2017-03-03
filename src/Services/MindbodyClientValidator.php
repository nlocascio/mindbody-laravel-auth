<?php

namespace Nlocascio\MindbodyAuth\Services;

use Illuminate\Support\Facades\Log;
use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;
use Nlocascio\Mindbody\Services\MindbodyService;
use Nlocascio\MindbodyAuth\Contracts\MindbodyValidator;

class MindbodyClientValidator implements MindbodyValidator
{
    /**
     * @var MindbodyService
     */
    private $mindbodyService;

    /**
     * MindbodyClientValidator constructor.
     *
     * @param MindbodyService $mindbodyService
     */
    public function __construct(MindbodyService $mindbodyService)
    {
        $this->mindbodyService = $mindbodyService;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function check($username, $password)
    {
        try {
            $response = $this->mindbodyService->ValidateLogin([
                'Username' => $username,
                'Password' => $password
            ]);
        } catch (MindbodyErrorException $e) {
            return false;
        }

        return $response->ErrorCode == 200;
    }
}