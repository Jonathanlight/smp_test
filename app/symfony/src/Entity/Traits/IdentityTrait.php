<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

trait IdentityTrait
{
    /**
     * @var string
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Regex("/^[\p{L}\-' ]+$/u")
     * @Serializer\Expose()
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Regex("/^[\p{L}\-' ]+$/u")
     * @Serializer\Expose()
     */
    protected $lastName;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(?string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(?string $lastName)
    {
        $this->lastName = $lastName;
    }
}
