<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @Serializer\ExclusionPolicy("all")
 */
class Course
{
    const TIME_START_AT = '8:00';
    const TIME_START_END = '12:00';
    const TIME_AFTER_AT = '14:00';
    const TIME_AFTER_END = '17:00';

    use IdentifiableTrait;
    use DeletableTrait;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2)
     * @Serializer\Expose()
     */
    protected $amount;

    /**
     * @var Center
     * @ORM\ManyToOne(targetEntity=Center::class, inversedBy="courses")
     */
    protected $center;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     * @Serializer\Expose()
     */
    protected $endOn;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $endMorningStartAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $endMorningEndAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $endAfternoonStartAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $endAfternoonEndAt;

    /**
     * @var Animator
     * @ORM\ManyToOne(targetEntity=Animator::class)
     */
    protected $former;

    /**
     * @var Order[]
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="course")
     */
    protected $orders;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $originalPrice;

    /**
     * @var Payment
     * @ORM\ManyToOne(targetEntity=Payment::class, inversedBy="courses")
     */
    protected $payment;

    /**
     * @var Place
     * @ORM\ManyToOne(targetEntity=Place::class)
     * @Serializer\Expose()
     */
    protected $place;

    /**
     * @var Animator
     * @ORM\ManyToOne(targetEntity=Animator::class)
     */
    protected $psychologist;

    /**
     * @var int
     * @Assert\Range(min="1", max="20")
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $confirmed = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $registered = 0;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $special = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     * @Serializer\Expose()
     */
    protected $startOn;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $startMorningStartAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $startMorningEndAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $startAfternoonStartAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $startAfternoonEndAt;

    public function __construct()
    {
        $this->startMorningStartAt = new \DateTime(self::TIME_START_AT);
        $this->startMorningEndAt = new \DateTime(self::TIME_START_END);
        $this->startAfternoonStartAt = new \DateTime(self::TIME_AFTER_AT);
        $this->startAfternoonEndAt = new \DateTime(self::TIME_AFTER_END);
        $this->endMorningStartAt = new \DateTime(self::TIME_START_AT);
        $this->endMorningEndAt = new \DateTime(self::TIME_START_END);
        $this->endAfternoonStartAt = new \DateTime(self::TIME_AFTER_AT);
        $this->endAfternoonEndAt = new \DateTime(self::TIME_AFTER_END);
        $this->orders = new ArrayCollection();
    }

    /**
     * @return float
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
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
    public function setCenter(Center $center): void
    {
        $this->center = $center;
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
     * @return \DateTime|null
     */
    public function getEndOn(): ?\DateTime
    {
        return $this->endOn;
    }

    /**
     * @param \DateTime|null $endOn
     */
    public function setEndOn(?\DateTime $endOn): void
    {
        $this->endOn = $endOn;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndMorningStartAt(): ?\DateTime
    {
        return $this->endMorningStartAt;
    }

    /**
     * @param \DateTime|null $endMorningStartAt
     */
    public function setEndMorningStartAt(?\DateTime $endMorningStartAt): void
    {
        $this->endMorningStartAt = $endMorningStartAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndMorningEndAt(): ?\DateTime
    {
        return $this->endMorningEndAt;
    }

    /**
     * @param \DateTime|null $endMorningEndAt
     */
    public function setEndMorningEndAt(?\DateTime $endMorningEndAt): void
    {
        $this->endMorningEndAt = $endMorningEndAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndAfternoonStartAt(): ?\DateTime
    {
        return $this->endAfternoonStartAt;
    }

    /**
     * @param \DateTime|null $endAfternoonStartAt
     */
    public function setEndAfternoonStartAt(?\DateTime $endAfternoonStartAt): void
    {
        $this->endAfternoonStartAt = $endAfternoonStartAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndAfternoonEndAt(): ?\DateTime
    {
        return $this->endAfternoonEndAt;
    }

    /**
     * @param \DateTime|null $endAfternoonEndAt
     */
    public function setEndAfternoonEndAt(?\DateTime $endAfternoonEndAt): void
    {
        $this->endAfternoonEndAt = $endAfternoonEndAt;
    }

    /**
     * @return Animator
     */
    public function getFormer(): ?Animator
    {
        return $this->former;
    }

    /**
     * @param Animator|null $former
     */
    public function setFormer(?Animator $former): void
    {
        $this->former = $former;
    }

    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @return float
     */
    public function getOriginalPrice(): ?float
    {
        return $this->originalPrice;
    }

    /**
     * @param float $originalPrice
     */
    public function setOriginalPrice(?float $originalPrice): void
    {
        $this->originalPrice = $originalPrice;
    }

    /**
     * @return Collection
     */
    public function getConfirmedOrders(): ?Collection
    {
        return $this->orders->filter(function ($order) {
            return Order::STATUS_CONFIRMED === $order->getStatus();
        });
    }

    /**
     * @return Collection
     */
    public function getConfirmedOrdersWithTrainee(): ?Collection
    {
        return $this->orders->filter(function ($order) {
            return Order::STATUS_CONFIRMED === $order->getStatus()
                && null !== $order->getTrainee()->getReference();
        });
    }

    /**
     * @return Collection
     */
    public function getConfirmedOrdersWithTraineeAndRegularCommission(): ?Collection
    {
        return $this->orders->filter(function ($order) {
            return Order::STATUS_CONFIRMED === $order->getStatus()
                && null !== $order->getTrainee()->getReference()
                && false === $order->isReducedCommission();
        });
    }

    /**
     * @return Collection
     */
    public function getConfirmedOrdersWithTraineeAndReducedCommission(): ?Collection
    {
        return $this->orders->filter(function ($order) {
            return Order::STATUS_CONFIRMED === $order->getStatus()
                && null !== $order->getTrainee()->getReference()
                && true === $order->isReducedCommission();
        });
    }

    /**
     * @return float
     */
    public function getCollectedFees(): ?float
    {
        $amount = 0;

        foreach ($this->getConfirmedOrders() as $order) {
            $amount += $order->getCommission();
        }

        return $amount;
    }

    /**
     * @return bool
     */
    public function isSpecial(): bool
    {
        return $this->special;
    }

    /**
     * @param bool $special
     */
    public function setSpecial(bool $special): void
    {
        $this->special = $special;
    }

    /**
     * @return Payment
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @return Place
     */
    public function getPlace(): ?Place
    {
        return $this->place;
    }

    /**
     * @param Place $place
     */
    public function setPlace(Place $place): void
    {
        $this->place = $place;
    }

    /**
     * @return Animator
     */
    public function getPsychologist(): ?Animator
    {
        return $this->psychologist;
    }

    /**
     * @param Animator|null $psychologist
     */
    public function setPsychologist(?Animator $psychologist): void
    {
        $this->psychologist = $psychologist;
    }

    /**
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getConfirmed(): ?int
    {
        return $this->confirmed;
    }

    /**
     * @param int $confirmed
     */
    public function setConfirmed(int $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return int
     */
    public function getRegistered(): ?int
    {
        return $this->registered;
    }

    /**
     * @param int $registered
     */
    public function setRegistered(int $registered): void
    {
        $this->registered = $registered;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartOn(): ?\DateTime
    {
        return $this->startOn;
    }

    /**
     * @param \DateTime|null $startOn
     */
    public function setStartOn(?\DateTime $startOn): void
    {
        $this->startOn = $startOn;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartMorningStartAt(): ?\DateTime
    {
        return $this->startMorningStartAt;
    }

    /**
     * @param \DateTime|null $startMorningStartAt
     */
    public function setStartMorningStartAt(?\DateTime $startMorningStartAt): void
    {
        $this->startMorningStartAt = $startMorningStartAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartMorningEndAt(): ?\DateTime
    {
        return $this->startMorningEndAt;
    }

    /**
     * @param \DateTime|null $startMorningEndAt
     */
    public function setStartMorningEndAt(?\DateTime $startMorningEndAt): void
    {
        $this->startMorningEndAt = $startMorningEndAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartAfternoonStartAt(): ?\DateTime
    {
        return $this->startAfternoonStartAt;
    }

    /**
     * @param \DateTime|null $startAfternoonStartAt
     */
    public function setStartAfternoonStartAt(?\DateTime $startAfternoonStartAt): void
    {
        $this->startAfternoonStartAt = $startAfternoonStartAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartAfternoonEndAt(): ?\DateTime
    {
        return $this->startAfternoonEndAt;
    }

    /**
     * @param \DateTime|null $startAfternoonEndAt
     */
    public function setStartAfternoonEndAt(?\DateTime $startAfternoonEndAt): void
    {
        $this->startAfternoonEndAt = $startAfternoonEndAt;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param $payload
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->startOn >= $this->endOn) {
            $context
                ->buildViolation('validator.course.endOn')
                ->atPath('endOn')
                ->addViolation()
            ;
        }

        if ($this->startOn <= new \DateTime('now')) {
            $context
                ->buildViolation('validator.course.startOn')
                ->atPath('startOn')
                ->addViolation()
            ;
        }
    }
}
