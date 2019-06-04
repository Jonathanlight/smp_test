<?php

namespace App\Event;

use App\Entity\Transfer;
use Symfony\Component\EventDispatcher\Event;

class TransferEvent extends Event
{
    const TRANSFER_CREATED = 'transfer.created';

    /**
     * @var Transfer
     */
    protected $transfer;

    /**
     * @param Transfer $transfer
     */
    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * @return Transfer
     */
    public function getTransfer()
    {
        return $this->transfer;
    }
}
