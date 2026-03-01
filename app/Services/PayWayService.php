<?php

namespace App\Services;

class PayWayService{
    /**
     * Get the API URL from the configuration.
     *
     * @return string
     */

    public function getApiUrl(){
        return config('payway.api_url');
    }

    public function getHash($string){
        $public_key = config('payway.public_key'); //* Sandbox Public key
        return base64_encode(hash_hmac('sha512',$string,$public_key,true));
    }
}