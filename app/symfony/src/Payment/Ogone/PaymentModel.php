<?php

namespace App\Payment\Ogone;

class PaymentModel
{
    /**
     * @var string
     */
    protected $pspId;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $shasign;

    /**
     * @var string
     */
    protected $email;

    /**
     * @return string
     */
    public function getPspId(): string
    {
        return $this->pspId;
    }

    /**
     * @param string $pspId
     */
    public function setPspId(string $pspId): void
    {
        $this->pspId = $pspId;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getShasign(): string
    {
        return $this->shasign;
    }

    /**
     * @param string $shasign
     */
    public function setShasign(string $shasign): void
    {
        $this->shasign = $shasign;
    }

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
}
