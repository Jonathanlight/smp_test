<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 */
class Tarif
{
    const TYPE_COURSE = 'course';
    const TYPE_DATE = 'date';

    use IdentifiableTrait;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $commission;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $commissionByCourse;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $commissionByDate;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $confirmedCourse = 0;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $endOn;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $startOn;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $totalCourse;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $type;

    /**
     * @return int
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
    public function getCommissionByCourse(): ?float
    {
        return $this->commissionByCourse;
    }

    /**
     * @param float $commissionByCourse
     */
    public function setCommissionByCourse(?float $commissionByCourse): void
    {
        $this->commissionByCourse = $commissionByCourse;
    }

    /**
     * @return float
     */
    public function getCommissionByDate(): ?float
    {
        return $this->commissionByDate;
    }

    /**
     * @param float $commissionByDate
     */
    public function setCommissionByDate(?float $commissionByDate): void
    {
        $this->commissionByDate = $commissionByDate;
    }

    /**
     * @return int
     */
    public function getConfirmedCourse(): ?int
    {
        return $this->confirmedCourse;
    }

    /**
     * @param int $confirmedCourse
     */
    public function setConfirmedCourse(int $confirmedCourse): void
    {
        $this->confirmedCourse = $confirmedCourse;
    }

    /**
     * @return \DateTime
     */
    public function getEndOn(): ?\DateTime
    {
        return $this->endOn;
    }

    /**
     * @param \DateTime $endOn
     */
    public function setEndOn(?\DateTime $endOn): void
    {
        $this->endOn = $endOn;
    }

    /**
     * @return \DateTime
     */
    public function getStartOn(): ?\DateTime
    {
        return $this->startOn;
    }

    /**
     * @param \DateTime $startOn
     */
    public function setStartOn(?\DateTime $startOn): void
    {
        $this->startOn = $startOn;
    }

    /**
     * @return int
     */
    public function getTotalCourse(): ?int
    {
        return $this->totalCourse;
    }

    /**
     * @param int $totalCourse
     */
    public function setTotalCourse(?int $totalCourse): void
    {
        $this->totalCourse = $totalCourse;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getRemainingCourse(): ?int
    {
        return $this->totalCourse - $this->confirmedCourse;
    }

    /**
     * @return bool
     */
    public function isDiscountDateActive(): ?bool
    {
        return $this->startOn <= new \DateTime(date('Y-m-d')) && $this->endOn >= new \DateTime(date('Y-m-d'));
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
                ->buildViolation('validator.date.endOn')
                ->atPath('endOn')
                ->addViolation()
            ;
        }

        if ($this->startOn <= new \DateTime('now')) {
            $context
                ->buildViolation('validator.date.startOn')
                ->atPath('startOn')
                ->addViolation()
            ;
        }
    }
}
