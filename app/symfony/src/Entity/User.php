<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity("username")
 * @Serializer\ExclusionPolicy("all")
 */
class User implements UserInterface
{
    use IdentifiableTrait;
    use DeletableTrait;

    const ROLE_CSSR = 'ROLE_CSSR';
    const ROLE_SMP = 'ROLE_SMP';
    const ROLE_CONSULTANT = 'ROLE_CONSULTANT';

    /**
     * @var Center
     * @ORM\ManyToOne(targetEntity=Center::class, inversedBy="users")
     * @Serializer\Expose()
     */
    protected $center;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @var string
     * @ORM\Column(length=96)
     */
    protected $password;

    /**
     * @var string
     * @Assert\Regex(
     *     pattern="/(?=(.*[0-9]))(?=.*[\!@#$%^&*()\\[\]{}\-_+=~`|:;'<>,.\/?])(?=.*[a-z])(?=(.*[A-Z]))(?=(.*)).{8,}/",
     *     message="validator.user.password"
     * )
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $role = self::ROLE_CSSR;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Token::class, mappedBy="user", cascade={"remove"})
     */
    protected $tokens;

    /**
     * @var string
     * @ORM\Column()
     * @Assert\Email()
     */
    protected $username;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
    }

    /**
     * @return Center
     */
    public function getCenter(): ?Center
    {
        return $this->center;
    }

    /**
     * @param Center $center
     */
    public function setCenter(?Center $center): void
    {
        $this->center = $center;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

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
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return Collection
     */
    public function getTokens(): ?Collection
    {
        return $this->tokens;
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
