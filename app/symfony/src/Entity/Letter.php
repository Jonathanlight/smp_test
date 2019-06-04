<?php

namespace App\Entity;

use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Letter
{
    use IdentifiableTrait;
    use CodifiableTrait;

    const MAIL_USER_RESET = 'user.reset';

    const MAIL_ORDER_PAID = 'order.paid';
    const MAIL_ORDER_CONFIRMED = 'order.confirmed';
    const MAIL_ORDER_REFUNDED = 'order.refunded';
    const MAIL_ORDER_WAITING = 'order.waiting';
    const MAIL_ORDER_CANCELLED = 'order.cancelled';

    const MAIL_TRANSFER_CREATED = 'transfer.created';
    const MAIL_TRANSFER_RECEIVED = 'transfer.received';

    const MAIL_PAYMENT_RECEIVED = 'payment.received';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $subject;

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
}
