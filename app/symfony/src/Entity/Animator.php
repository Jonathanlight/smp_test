<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\GeolocableTrait;
use App\Entity\Traits\IdentifiableTrait;
use App\Entity\Traits\IdentityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 */
class Animator
{
    use IdentifiableTrait;
    use IdentityTrait;
    use GeolocableTrait;
    use DeletableTrait;

    const DEGREE_PSYCHOLOGIST = 'psychologist';
    const DEGREE_BAFM_BAFCRI = 'former';

    /**
     * @var Center
     * @ORM\ManyToOne(targetEntity=Center::class)
     */
    protected $center;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $degree;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Regex("/^(?:(?:\+|00)33|0)\s*[6-7](?:[\s.-]*\d{2}){4}$/")
     */
    protected $mobile;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Regex("/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/")
     */
    protected $phone;

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
    public function setCenter(Center $center): void
    {
        $this->center = $center;
    }

    /**
     * @return string
     */
    public function getDegree(): ?string
    {
        return $this->degree;
    }

    /**
     * @param string $degree
     */
    public function setDegree(string $degree): void
    {
        $this->degree = $degree;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
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
     * @return string
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param $payload
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null === $this->mobile && null === $this->phone) {
            $context
                ->buildViolation('validator.animator.mobile')
                ->atPath('mobile')
                ->addViolation()
            ;
        }
    }
}
