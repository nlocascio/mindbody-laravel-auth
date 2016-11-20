<?php

namespace Nlocascio\MindbodyAuth\Traits;

trait AuthenticatesWithMindbody {

    /**
     * @return mixed
     */
    public function getMindbodySiteId()
    {
        return config('mindbody_auth.site_id');
    }

    /**
     * This function is called whenever the user successfully logs in.
     * $data will be filled with their MINDBODY profile information.
     * You may choose to persist it here.
     *
     * @param $data
     */
    public function fillMindbodyData($data)
    {
//        $this->fill([
//            'name' => $data['FirstName'] ." ". $data['LastName']
//        ]);
        return;
    }
}
