<?php

namespace App\Manager;

use App\Entity\Course;
use App\Entity\Order;
use App\Entity\Transfer;
use App\Event\TransferEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TransferManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface   $em
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Order  $order
     * @param Course $course
     */
    public function create(Order $order, Course $course): void
    {
        $transfer = new Transfer();

        $transfer->setOrder($order);
        $transfer->setPreviousCourse($order->getCourse());
        $transfer->setNewCourse($course);
        $transfer->setTransferedAt(new \DateTime());

        $this->eventDispatcher->dispatch(
            TransferEvent::TRANSFER_CREATED,
            new TransferEvent($transfer)
        );

        $this->em->persist($transfer);
        $this->em->flush();
    }
}
