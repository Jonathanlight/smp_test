<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Delivery
{
    use IdentifiableTrait;

    /**
     * @var string
     * @ORM\Column()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var Letter
     * @ORM\ManyToOne(targetEntity=Letter::class)
     */
    protected $letter;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $sentAt;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Letter
     */
    public function getLetter(): Letter
    {
        return $this->letter;
    }

    /**
     * @param Letter $letter
     */
    public function setLetter(Letter $letter): void
    {
        $this->letter = $letter;
    }

    /**
     * @return \DateTime
     */
    public function getSentAt(): \DateTime
    {
        return $this->sentAt;
    }

    /**
     * @param \DateTime $sentAt
     */
    public function setSentAt(\DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }
}
