<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Token
{
    use IdentifiableTrait;

    const TYPE_RESET = 'reset';

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expiredAt;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $type = self::TYPE_RESET;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tokens", cascade={"remove"})
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $value;

    /**
     * @return \DateTime
     */
    public function getExpiredAt(): \DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @param \DateTime $expiredAt
     */
    public function setExpiredAt(\DateTime $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
