<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    use IdentifiableTrait;

    const STATUS_TECHNICAL_PROBLEM = 0;
    const STATUS_CANCELED_TRANSACTION = 1;
    const STATUS_REFUSED_PAYMENT = 2;
    const STATUS_ACCEPTED_PAYMENT = 5;

    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="transactions", cascade={"persist"})
     */
    protected $order;

    /**
     * @var int
     * @ORM\Column(type="bigint")
     */
    protected $ogoneId;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $amount = 0;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $cardType;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $namePaymentCard;

    /**
     * @var string
     * @ORM\Column(type="integer")
     */
    protected $codeError;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $meansPayment;

    /**
     * @var string
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    protected $date;

    /**
     * @return Order
     */
    public function getOrder(): Order
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
     * @return int
     */
    public function getOgoneId(): int
    {
        return $this->ogoneId;
    }

    /**
     * @param int $ogoneId
     */
    public function setOgoneId(int $ogoneId): void
    {
        $this->ogoneId = $ogoneId;
    }

    /**
     * @return float
     */
    public function getAmount(): float
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
     * @return string
     */
    public function getCardType(): string
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     */
    public function setCardType(string $cardType): void
    {
        $this->cardType = $cardType;
    }

    /**
     * @return string
     */
    public function getNamePaymentCard(): string
    {
        return $this->namePaymentCard;
    }

    /**
     * @param string $namePaymentCard
     */
    public function setNamePaymentCard(string $namePaymentCard): void
    {
        $this->namePaymentCard = $namePaymentCard;
    }

    /**
     * @return string
     */
    public function getCodeError(): string
    {
        return $this->codeError;
    }

    /**
     * @param string $codeError
     */
    public function setCodeError(string $codeError): void
    {
        $this->codeError = $codeError;
    }

    /**
     * @return string
     */
    public function getMeansPayment(): string
    {
        return $this->meansPayment;
    }

    /**
     * @param string $meansPayment
     */
    public function setMeansPayment(string $meansPayment): void
    {
        $this->meansPayment = $meansPayment;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }
}
