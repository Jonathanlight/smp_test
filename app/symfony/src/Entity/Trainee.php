<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\GeolocableTrait;
use App\Entity\Traits\IdentifiableTrait;
use App\Entity\Traits\IdentityTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @Serializer\ExclusionPolicy("all")
 */
class Trainee
{
    const TYPE_VOLUNTARY = 'voluntary';
    const TYPE_REQUIRED = 'required';
    const TYPE_JUDICIAL = 'judicial';
    const TYPE_SENTENCE = 'sentence';

    const NEW_FORMAT_DRIVER_LICENCE = 'new';
    const OLD_FORMAT_DRIVER_LICENCE = 'old';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    use IdentifiableTrait;
    use IdentityTrait;
    use GeolocableTrait;
    use DeletableTrait;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $civility;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(nullable=true)
     */
    protected $placeBirth;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\Date()
     * @ORM\Column(type="date", nullable=true)
     */
    protected $dateBirth;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $courseType;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $deliveredAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $deliveredOn;

    /**
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $driverLicense;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $formatDriverLicense;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $obtainedOn;

    /**
     * @var Order
     * @ORM\OneToOne(targetEntity=Order::class)
     */
    protected $order;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank()
     * @Assert\Regex("/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/")
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $reference;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $sendDriver = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $sendLetter = false;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $driverPoint;

    /**
     * @var string
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $driverPointUnknown;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $placeInfraction;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $datePlaceInfraction;

    /**
     * @var \DateTime
     * @ORM\Column(type="time", nullable=true)
     */
    protected $hourPlaceInfraction;

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
    public function getCivility(): ?string
    {
        return $this->civility;
    }

    /**
     * @param string $civility
     */
    public function setCivility(string $civility): void
    {
        $this->civility = $civility;
    }

    /**
     * @return string
     */
    public function getPlaceBirth(): ?string
    {
        return $this->placeBirth;
    }

    /**
     * @param string $placeBirth
     */
    public function setPlaceBirth(string $placeBirth): void
    {
        $this->placeBirth = $placeBirth;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateBirth(): ?\DateTime
    {
        return $this->dateBirth;
    }

    /**
     * @param \DateTime $dateBirth
     */
    public function setDateBirth(?\DateTime $dateBirth): void
    {
        $this->dateBirth = $dateBirth;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
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
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
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
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getCourseType(): ?string
    {
        return $this->courseType;
    }

    /**
     * @param string $courseType
     */
    public function setCourseType(string $courseType)
    {
        $this->courseType = $courseType;
    }

    /**
     * @return string
     */
    public function getDeliveredAt(): ?string
    {
        return $this->deliveredAt;
    }

    /**
     * @param string|null $deliveredAt
     */
    public function setDeliveredAt(?string $deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeliveredOn(): ?\DateTime
    {
        return $this->deliveredOn;
    }

    /**
     * @param \DateTime|null $deliveredOn
     */
    public function setDeliveredOn(?\DateTime $deliveredOn)
    {
        $this->deliveredOn = $deliveredOn;
    }

    /**
     * @return string
     */
    public function getDriverLicense(): ?string
    {
        return $this->driverLicense;
    }

    /**
     * @param string|null $driverLicense
     */
    public function setDriverLicense(?string $driverLicense)
    {
        $this->driverLicense = $driverLicense;
    }

    /**
     * @return \DateTime|null
     */
    public function getObtainedOn(): ?\DateTime
    {
        return $this->obtainedOn;
    }

    /**
     * @param \DateTime|null $obtainedOn
     */
    public function setObtainedOn(?\DateTime $obtainedOn)
    {
        $this->obtainedOn = $obtainedOn;
    }

    /**
     * @return string
     */
    public function getFormatDriverLicense(): ?string
    {
        return $this->formatDriverLicense;
    }

    /**
     * @param string|null $formatDriverLicense
     */
    public function setFormatDriverLicense(?string $formatDriverLicense): void
    {
        $this->formatDriverLicense = $formatDriverLicense;
    }

    /**
     * @return bool
     */
    public function getSendDriver(): ?bool
    {
        return $this->sendDriver;
    }

    /**
     * @param bool $sendDriver
     */
    public function setSendDriver(bool $sendDriver): void
    {
        $this->sendDriver = $sendDriver;
    }

    /**
     * @return bool
     */
    public function getSendLetter(): ?bool
    {
        return $this->sendLetter;
    }

    /**
     * @param bool $sendLetter
     */
    public function setSendLetter(bool $sendLetter): void
    {
        $this->sendLetter = $sendLetter;
    }

    /**
     * @return int
     */
    public function getDriverPoint(): ?int
    {
        return $this->driverPoint;
    }

    /**
     * @param int $driverPoint
     */
    public function setDriverPoint(?int $driverPoint): void
    {
        $this->driverPoint = $driverPoint;
    }

    /**
     * @return int
     */
    public function getDriverPointUnknown(): ?int
    {
        return $this->driverPointUnknown;
    }

    /**
     * @param string|null $driverPointUnknown
     */
    public function setDriverPointUnknown(?string $driverPointUnknown): void
    {
        $this->driverPointUnknown = $driverPointUnknown;
    }

    /**
     * @return string
     */
    public function getPlaceInfraction(): ?string
    {
        return $this->placeInfraction;
    }

    /**
     * @param string|null $placeInfraction
     */
    public function setPlaceInfraction(?string $placeInfraction): void
    {
        $this->placeInfraction = $placeInfraction;
    }

    /**
     * @return \DateTime|null
     */
    public function getDatePlaceInfraction(): ?\DateTime
    {
        return $this->datePlaceInfraction;
    }

    /**
     * @param \DateTime|null $datePlaceInfraction
     */
    public function setDatePlaceInfraction(?\DateTime $datePlaceInfraction): void
    {
        $this->datePlaceInfraction = $datePlaceInfraction;
    }

    /**
     * @return \DateTime|null
     */
    public function getHourPlaceInfraction(): ?\DateTime
    {
        return $this->hourPlaceInfraction;
    }

    /**
     * @param \DateTime|null $hourPlaceInfraction
     */
    public function setHourPlaceInfraction(?\DateTime $hourPlaceInfraction): void
    {
        $this->hourPlaceInfraction = $hourPlaceInfraction;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param $payload
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null !== $this->dateBirth && $this->dateBirth > new \DateTime('-18 years')) {
            $context
                ->buildViolation('validator.trainee.dateBirth')
                ->atPath('dateBirth')
                ->addViolation()
            ;
        }

        if ($this->order->getCourse()->getStartOn() < new \DateTime(date('Y-m-d', strtotime('+2 day')))) {
            if (null === $this->email) {
                $context
                    ->buildViolation('validator.trainee.email')
                    ->atPath('email')
                    ->addViolation()
                ;
            }
        }
    }
}
