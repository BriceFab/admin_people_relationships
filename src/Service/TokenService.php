<?php

namespace App\Service;

/**
 * Class TokenService
 * @package App\Service
 */
class TokenService
{
    public function generateRandomString($length = 16)
    {
        return substr(sha1(rand()), 0, $length);
    }
}
