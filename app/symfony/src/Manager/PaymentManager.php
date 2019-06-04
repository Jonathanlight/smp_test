<?php

namespace App\Manager;

use App\Entity\Center;
use App\Entity\Payment;
use App\Event\PaymentEvent;
use App\Repository\PaymentRepository;
use App\Services\PaginatorService;
use App\Services\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var PaymentRepository
     */
    protected $repository;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @var PaginatorService
     */
    protected $paginatorService;

    /**
     * PaymentManager constructor.
     *
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param PaginatorService         $paginatorService
     * @param PaymentService           $paymentService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        PaginatorService $paginatorService,
        PaymentService $paymentService
    ) {
        $this->em = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $this->em->getRepository(Payment::class);
        $this->paymentService = $paymentService;
        $this->paginatorService = $paginatorService;
    }

    /**
     * @param Center     $center
     * @param array|null $filters
     *
     * @return PaginationInterface
     */
    public function collect(Center $center, ?array $filters): PaginationInterface
    {
        return $this->paginatorService->paginate(
            $this->paymentService->groupByDate($this->repository->collect($center, $filters)),
            PaginatorService::DEFAULT_LIMIT,
            $filters['page'] ?: PaginatorService::DEFAULT_PAGE
        );
    }

    /**
     * @param Center $center
     * @param float  $amount
     *
     * @return Payment
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(Center $center, float $amount): Payment
    {
        $payment = new Payment();

        $payment->setCenter($center);
        $payment->setGeneratedAt(new \DateTime());
        $payment->setStatus(Payment::STATUS_PAID);
        $payment->setAmount($amount);
        $payment->setReference($this->generateReference($center));

        $this->eventDispatcher->dispatch(
            PaymentEvent::PAYMENT_RECEIVED,
            new PaymentEvent($payment)
        );

        $this->em->persist($payment);
        $this->em->flush();

        return $payment;
    }

    /**
     * @param Center $center
     *
     * @return string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function generateReference(Center $center): string
    {
        $parts = [];

        $parts[] = 'S';
        $parts[] = date('Y');
        $parts[] = date('m');
        $parts[] = '-';
        $parts[] = $center->getCode();
        $parts[] = '-';

        $payment = $this->repository->getLatest(join('', $parts));
        $number = 1;

        if ($payment instanceof Payment) {
            $number = intval(str_pad(mb_substr($payment->getReference(), -5), 5, '', STR_PAD_LEFT));
            ++$number;
        }

        $parts[] = str_pad($number, 5, 0, STR_PAD_LEFT);

        return join('', $parts);
    }
}
