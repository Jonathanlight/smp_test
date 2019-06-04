<?php

namespace App\Services;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordService
{
    /**
     * @var PasswordService
     */
    private $userPasswordEncoder;

    /**
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param UserInterface $user
     * @param string        $password
     *
     * @return string
     */
    public function encode(UserInterface $user, string $password): string
    {
        return $this->userPasswordEncoder->encodePassword($user, $password);
    }

    /**
     * @param UserInterface $user
     * @param string        $password
     *
     * @return bool
     */
    public function isValid(UserInterface $user, string $password): bool
    {
        return $this->userPasswordEncoder->isPasswordValid($user, $password);
    }
}
