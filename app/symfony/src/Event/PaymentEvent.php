<?php

namespace App\Event;

use App\Entity\Payment;
use Symfony\Component\EventDispatcher\Event;

class PaymentEvent extends Event
{
    const PAYMENT_RECEIVED = 'payment.received';

    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
