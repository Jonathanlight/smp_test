<?php

namespace App\Services;

class TokenService
{
    /**
     * @param int $length
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generate(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function isValid($token): bool
    {
        return new \DateTime() < $token->getExpiredAt();
    }
}
