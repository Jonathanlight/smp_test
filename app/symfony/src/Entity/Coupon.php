<?php

namespace App\Entity;

use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @UniqueEntity("code")
 */
class Coupon
{
    use IdentifiableTrait;
    use CodifiableTrait;

    const COUPON_AMOUNT = 4.95;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $amount;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $endAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $startAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

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
     * @return \DateTime
     */
    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    /**
     * @param \DateTime $endAt
     */
    public function setEndAt(\DateTime $endAt): void
    {
        $this->endAt = $endAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    /**
     * @param \DateTime $startAt
     */
    public function setStartAt(\DateTime $startAt): void
    {
        $this->startAt = $startAt;
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
     * @param ExecutionContextInterface $context
     * @param $payload
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->amount > self::COUPON_AMOUNT) {
            $context
                ->buildViolation('validator.coupon.amount')
                ->atPath('amount')
                ->addViolation()
            ;
        }

        if ($this->amount < 0) {
            $context
                ->buildViolation('validator.coupon.amountUnderZero')
                ->atPath('amount')
                ->addViolation()
            ;
        }
    }
}
