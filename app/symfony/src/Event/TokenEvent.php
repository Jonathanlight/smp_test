<?php

namespace App\Event;

use App\Entity\Token;
use Symfony\Component\EventDispatcher\Event;

class TokenEvent extends Event
{
    const TOKEN_GENERATE = 'token.generate';

    /**
     * @var Token
     */
    protected $token;

    /**
     * TokenEvent constructor.
     *
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}
