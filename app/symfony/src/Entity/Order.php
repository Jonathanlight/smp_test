<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 * @Serializer\ExclusionPolicy("all")
 */
class Order
{
    use IdentifiableTrait;

    const STATUS_REGISTERED = 'registered';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_WAITING = 'waiting';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PENDING = 'pending';

    /**
     * @var Course
     * @ORM\ManyToOne(targetEntity=Course::class, inversedBy="orders", cascade={"persist"})
     * @Serializer\Expose()
     */
    protected $course;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity=Export::class, cascade={"persist"})
     */
    protected $exports;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $commission;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $refundedAmount;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $discountAmount;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $optionsAmount;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     * @Serializer\Expose()
     */
    protected $fee;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $letter;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Expose()
     */
    protected $paidAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $confirmedAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $cancelledAt;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $reference;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $reason;

    /**
     * @var Trainee
     * @ORM\OneToOne(targetEntity=Trainee::class, cascade={"persist"})
     * @Serializer\Expose()
     */
    protected $trainee;

    /**
     * @var Option[]
     * @ORM\ManyToMany(targetEntity=Option::class, cascade={"persist"})
     * @Serializer\Expose()
     */
    protected $options;

    /**
     * @var Coupon
     * @ORM\ManyToOne(targetEntity=Coupon::class, cascade={"persist"})
     */
    protected $coupon;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    protected $amount;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="order", cascade={"persist"})
     */
    protected $transactions;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $reducedCommission = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $refundAt;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->exports = new ArrayCollection();
    }

    /**
     * @param Option $option
     */
    public function addOption(Option $option): void
    {
        if ($this->options->contains($option)) {
            return;
        }

        $this->options->add($option);
    }

    /**
     * @param Option $option
     */
    public function removeOption(Option $option)
    {
        if (!$this->options->contains($option)) {
            return;
        }

        $this->options->removeElement($option);
    }

    /**
     * @param Option $option
     *
     * @return bool
     */
    public function hasOption(Option $option): bool
    {
        return $this->options->contains($option);
    }

    /**
     * @return Collection
     */
    public function getOptions(): ?Collection
    {
        return $this->options;
    }

    /**
     * @return Coupon
     */
    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    /**
     * @param Coupon $coupon
     */
    public function setCoupon(?Coupon $coupon): void
    {
        $this->coupon = $coupon;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
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
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @return Trainee
     */
    public function getTrainee(): ?Trainee
    {
        return $this->trainee;
    }

    /**
     * @param Trainee $trainee
     */
    public function setTrainee(Trainee $trainee): void
    {
        $this->trainee = $trainee;
    }

    /**
     * @return Course
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     */
    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * @return \DateTime
     */
    public function getPaidAt(): ?\DateTime
    {
        return $this->paidAt;
    }

    /**
     * @param \DateTime $paidAt
     */
    public function setPaidAt(\DateTime $paidAt): void
    {
        $this->paidAt = $paidAt;
    }

    /**
     * @return \DateTime
     */
    public function getConfirmedAt(): ?\DateTime
    {
        return $this->confirmedAt;
    }

    /**
     * @param \DateTime $confirmedAt
     */
    public function setConfirmedAt(\DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @return float
     */
    public function getRefundedAmount(): ?float
    {
        return $this->refundedAmount;
    }

    /**
     * @param float $refundedAmount
     */
    public function setRefundedAmount(?float $refundedAmount): void
    {
        $this->refundedAmount = $refundedAmount;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): ?float
    {
        return $this->discountAmount;
    }

    /**
     * @param float $discountAmount
     */
    public function setDiscountAmount(?float $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     * @return float
     */
    public function getOptionsAmount(): ?float
    {
        return $this->optionsAmount;
    }

    /**
     * @param float $optionsAmount
     */
    public function setOptionsAmount(?float $optionsAmount): void
    {
        $this->optionsAmount = $optionsAmount;
    }

    /**
     * @return float
     */
    public function getFee(): ?float
    {
        return $this->fee;
    }

    /**
     * @param float $fee
     */
    public function setFee(?float $fee): void
    {
        $this->fee = $fee;
    }

    /**
     * @return float
     */
    public function getLetter(): ?float
    {
        return $this->letter;
    }

    /**
     * @param float $letter
     */
    public function setLetter(?float $letter): void
    {
        $this->letter = $letter;
    }

    /**
     * @return \DateTime
     */
    public function getCancelledAt(): ?\DateTime
    {
        return $this->cancelledAt;
    }

    /**
     * @param \DateTime $cancelledAt
     */
    public function setCancelledAt(\DateTime $cancelledAt): void
    {
        $this->cancelledAt = $cancelledAt;
    }

    /**
     * @return float
     */
    public function getCommission(): ?float
    {
        return $this->commission;
    }

    /**
     * @param float $commission
     */
    public function setCommission(float $commission): void
    {
        $this->commission = $commission;
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
    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * return float.
     */
    public function getAmountWithoutOptions()
    {
        $amount = $this->amount;

        $amount -= $this->optionsAmount;
        $amount += $this->discountAmount;

        return $amount;
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction)
    {
        if ($this->transactions->contains($transaction)) {
            return;
        }

        $this->transactions->add($transaction);
    }

    /**
     * @return Collection
     */
    public function getTransactions(): ?Collection
    {
        return $this->transactions;
    }

    /**
     * @return bool
     */
    public function isReducedCommission(): bool
    {
        return $this->reducedCommission;
    }

    /**
     * @param bool $reducedCommission
     */
    public function setReducedCommission(bool $reducedCommission): void
    {
        $this->reducedCommission = $reducedCommission;
    }

    /**
     * @return \DateTime
     */
    public function getRefundAt(): \DateTime
    {
        return $this->refundAt;
    }

    /**
     * @param \DateTime $refundAt
     */
    public function setRefundAt(\DateTime $refundAt): void
    {
        $this->refundAt = $refundAt;
    }

    /**
     * @param Export $export
     *
     * @return bool
     */
    public function hasExport(Export $export): bool
    {
        return $this->options->contains($export);
    }

    /**
     * @param Export $export
     */
    public function addExport(Export $export): void
    {
        if ($this->exports->contains($export)) {
            return;
        }

        $this->exports->add($export);
    }

    /**
     * @return Collection
     */
    public function getExports(): ?Collection
    {
        return $this->exports;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasExportType(string $type): bool
    {
        foreach ($this->getExports() as $export) {
            if ($type === $export->getType()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getDateExportType(string $type): string
    {
        foreach ($this->getExports() as $export) {
            if ($type === $export->getType()) {
                return $export->getExportedAt()->format('d/m/Y');
            }
        }

        return null;
    }
}
