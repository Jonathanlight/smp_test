<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 */
class Admin implements UserInterface
{
    use IdentifiableTrait;

    /**
     * @var string
     * @ORM\Column(length=96)
     */
    protected $password;

    /**
     * @var string
     */
    protected $role = 'ROLE_SUPER_ADMIN';

    /**
     * @var string
     * @ORM\Column()
     */
    protected $username;

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }
}
