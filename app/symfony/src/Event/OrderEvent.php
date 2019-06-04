<?php

namespace App\Event;

use App\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event
{
    const ORDER_PAID = 'order.paid';
    const ORDER_CONFIRMED = 'order.confirmed';
    const ORDER_REFUNDED = 'order.refunded';
    const ORDER_WAITING = 'order.waiting';
    const ORDER_CANCELLED = 'order.cancelled';
    const ORDER_OPTION_SUBSCRIBED = 'option.subscribed';

    /**
     * @var Order
     */
    protected $order;

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
