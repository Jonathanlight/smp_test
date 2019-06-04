<?php

namespace App\Manager;

use App\Entity\Coupon;
use App\Entity\Course;
use App\Entity\Option;
use App\Entity\Order;
use App\Entity\Tarif;
use App\Entity\Trainee;
use App\Entity\User;
use App\Event\OrderEvent;
use App\Payment\Ogone\PaymentModel;
use App\Repository\CouponRepository;
use App\Repository\OptionRepository;
use App\Repository\OrderRepository;
use App\Repository\TraineeRepository;
use App\Services\MessageService;
use App\Payment\Ogone\OgonePaymentService;
use App\Services\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Snappy\Pdf;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class OrderManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var OrderRepository
     */
    protected $repository;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var PaginatorService
     */
    protected $paginatorService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var TraineeRepository
     */
    protected $traineeRepository;

    /**
     * @var CouponRepository
     */
    protected $couponRepository;

    /**
     * @var OptionRepository
     */
    protected $optionRepository;

    /**
     * @var OgonePaymentService
     */
    protected $paymentService;

    /**
     * @var TraineeManager
     */
    protected $traineeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var TransferManager
     */
    protected $transferManager;

    /**
     * @var Pdf
     */
    protected $pdfGenerator;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param SessionInterface         $session
     * @param EngineInterface          $engine
     * @param PaginatorService         $paginatorService
     * @param MessageService           $messageService
     * @param EventDispatcherInterface $eventDispatcher
     * @param TraineeRepository        $traineeRepository
     * @param CouponRepository         $couponRepository
     * @param OptionRepository         $optionRepository
     * @param OgonePaymentService      $paymentService
     * @param LoggerInterface          $logger
     * @param TranslatorInterface      $translator
     * @param TraineeManager           $traineeManager
     * @param TransferManager          $transferManager
     * @param Pdf                      $pdfGenerator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        EngineInterface $engine,
        PaginatorService $paginatorService,
        MessageService $messageService,
        EventDispatcherInterface $eventDispatcher,
        TraineeRepository $traineeRepository,
        CouponRepository $couponRepository,
        OptionRepository $optionRepository,
        OgonePaymentService $paymentService,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        TraineeManager $traineeManager,
        TransferManager $transferManager,
        Pdf $pdfGenerator
    ) {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Order::class);
        $this->session = $session;
        $this->engine = $engine;
        $this->messageService = $messageService;
        $this->paginatorService = $paginatorService;
        $this->eventDispatcher = $eventDispatcher;
        $this->traineeRepository = $traineeRepository;
        $this->couponRepository = $couponRepository;
        $this->optionRepository = $optionRepository;
        $this->paymentService = $paymentService;
        $this->traineeManager = $traineeManager;
        $this->transferManager = $transferManager;
        $this->pdfGenerator = $pdfGenerator;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param array|null $filters
     *
     * @return PaginationInterface
     */
    public function collect(?array $filters): PaginationInterface
    {
        return $this->paginatorService->paginate(
            $this->repository->search($filters),
            PaginatorService::DEFAULT_LIMIT,
            isset($filters['page']) ? $filters['page'] : PaginatorService::DEFAULT_PAGE
        );
    }

    /**
     * @param Order $order
     * @param int   $step
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function update(Order $order, ?int $step = null): void
    {
        switch ($step) {
            case 1:
            case 3:
                $this->calculate($order);
                break;
            case 2:
                $this->addApplicationFee($order);
                $this->calculate($order);
                break;
            case 4:
                $course = $order->getCourse();

                $order->setStatus(Order::STATUS_REGISTERED);
                $order->setPaidAt(new \DateTime());
                $order->setReference($this->generateReference());

                $course->setRegistered($course->getRegistered() + 1);

                $this->traineeManager->updateReference($order->getTrainee());
                $this->calculateCommission($order, false);

                $this->eventDispatcher->dispatch(
                    OrderEvent::ORDER_PAID,
                    new OrderEvent($order)
                );

                $this->clear();
                break;
        }

        $this->em->flush();
    }

    /**
     * @param Order $order
     */
    public function refund(Order $order): void
    {
        if (Order::STATUS_CONFIRMED === $order->getStatus()) {
            $course = $order->getCourse();
            $course->setQuantity($course->getQuantity() + 1);
            $course->setConfirmed($course->getConfirmed() - 1);
        } elseif (Order::STATUS_REGISTERED === $order->getStatus()) {
            $course->setRegistered($course->getRegistered() - 1);
        }

        $order->setStatus(Order::STATUS_REFUNDED);
        $order->setRefundAt(new \DateTime());

        $this->em->flush();

        $this->eventDispatcher->dispatch(
            OrderEvent::ORDER_REFUNDED,
            new OrderEvent($order)
        );
    }

    /**
     * @param Course $course
     *
     * @return array
     */
    public function getRegisteredByCourse(Course $course): array
    {
        return $this->repository->getRegisteredByCourse($course);
    }

    /**
     * @param Order  $order
     * @param Course $course
     */
    public function transfer(Order $order, Course $course): void
    {
        $this->transferManager->create($order, $course);

        if (Order::STATUS_CONFIRMED === $order->getStatus()) {
            $previousCourse = $order->getCourse();
            $previousCourse->setQuantity($previousCourse->getQuantity() + 1);
            $previousCourse->setConfirmed($previousCourse->getConfirmed() - 1);
        } elseif (Order::STATUS_REGISTERED === $order->getStatus()) {
            $previousCourse->setRegistered($previousCourse->getRegistered() - 1);
        }

        $order->setCourse($course);
        $order->setStatus(Order::STATUS_REGISTERED);

        $this->calculateCommission($order, true);

        $this->em->flush();
    }

    /**
     * @param Trainee $trainee
     *
     * @return array
     */
    public function collectByTrainee(Trainee $trainee): array
    {
        return $this->repository->findByTrainee($trainee);
    }

    /**
     * @param Order $order
     */
    public function confirm(Order $order): void
    {
        $course = $order->getCourse();

        $course->setConfirmed($course->getConfirmed() + 1);

        if (Order::STATUS_REGISTERED === $order->getStatus()) {
            $course->setRegistered($course->getRegistered() - 1);
        }

        $order->setStatus(Order::STATUS_CONFIRMED);
        $order->setConfirmedAt(new \DateTime());

        $this->em->flush();

        $this->eventDispatcher->dispatch(
            OrderEvent::ORDER_CONFIRMED,
            new OrderEvent($order)
        );
    }

    /**
     * @param Order  $order
     * @param string $reason
     */
    public function cancel(Order $order, ?string $reason): void
    {
        if (Order::STATUS_CONFIRMED === $order->getStatus()) {
            $course = $order->getCourse();
            $course->setQuantity($course->getQuantity() + 1);
            $course->setConfirmed($course->getConfirmed() - 1);
        } elseif (Order::STATUS_REGISTERED === $order->getStatus()) {
            $course->setRegistered($course->getRegistered() - 1);
        }

        $order->setReason($reason);
        $order->setCancelledAt(new \DateTime());
        $order->setStatus(Order::STATUS_CANCELLED);

        $this->em->flush();

        $this->eventDispatcher->dispatch(
            OrderEvent::ORDER_CANCELLED,
            new OrderEvent($order)
        );
    }

    /**
     * @param Order $order
     */
    public function wait(Order $order): void
    {
        if (Order::STATUS_CONFIRMED === $order->getStatus()) {
            $course = $order->getCourse();
            $course->setQuantity($course->getQuantity() + 1);
            $course->setConfirmed($course->getConfirmed() - 1);
        } elseif (Order::STATUS_REGISTERED === $order->getStatus()) {
            $course->setRegistered($course->getRegistered() - 1);
        }

        $order->setStatus(Order::STATUS_WAITING);

        $this->em->flush();

        $this->eventDispatcher->dispatch(
            OrderEvent::ORDER_WAITING,
            new OrderEvent($order)
        );
    }

    /**
     * @param Trainee|null       $trainee
     * @param Course|null        $course
     * @param string             $status
     * @param FormInterface|null $form
     *
     * @return Order
     */
    public function create(?Trainee $trainee, ?Course $course, string $status = Order::STATUS_PENDING, ?FormInterface $form = null): Order
    {
        $order = new Order();

        $trainee->setCourseType(Trainee::TYPE_VOLUNTARY);
        $trainee->setFormatDriverLicense(Trainee::OLD_FORMAT_DRIVER_LICENCE);

        $order->setCourse($course);
        $order->setTrainee($trainee);
        $order->setStatus($status);

        $this->em->persist($trainee);
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @param Order $order
     */
    public function calculate(Order $order): void
    {
        $amount = 0;
        $fee = 0;
        $letter = 0;
        $options = 0;

        $amount += $order->getCourse()->getAmount();

        foreach ($order->getOptions() as $option) {
            $amount += $option->getAmount();
            $options += $option->getAmount();
            if (Option::CODE_APPLICATION_FEE === $option->getCode()) {
                $fee += $option->getAmount();
            }
            if (Option::CODE_SEND_LETTER === $option->getCode()) {
                $letter += $option->getAmount();
            }
        }

        $order->setOptionsAmount($options);

        if ($coupon = $order->getCoupon()) {
            $order->setDiscountAmount($coupon->getAmount());
            $amount -= $coupon->getAmount();
            $fee = 0;
        }

        $order->setLetter($letter);
        $order->setAmount($amount);
        $order->setFee($fee);
    }

    /**
     * @param Order $order
     * @param bool  $force
     */
    public function calculateCommission(Order $order, bool $force = false): void
    {
        if ((!$order->getTrainee()->getReference() || null !== $order->getCommission()) && false === $force) {
            return;
        }

        $tarif = $order->getCourse()->getCenter()->getTarif();

        if ($order->getTrainee()->getReference()) {
            if (Tarif::TYPE_DATE === $tarif->getType()
                && $tarif->isDiscountDateActive()) {
                $order->setCommission($tarif->getCommissionByDate());
                $order->setReducedCommission(true);
            } elseif (Tarif::TYPE_COURSE === $tarif->getType() && $tarif->getRemainingCourse()) {
                $order->setCommission($tarif->getCommissionByCourse());
                $tarif->setConfirmedCourse($tarif->getConfirmedCourse() + 1);
                $order->setReducedCommission(true);
            } else {
                $order->setCommission($tarif->getCommission());
                $order->setReducedCommission(false);
            }
        }
    }

    /**
     * @return Order
     */
    public function load(): ?Order
    {
        if (!$this->session->has('order')) {
            return null;
        }

        return $this->repository->find($this->session->get('order'));
    }

    /**
     * @param Course $course
     */
    public function generate(Course $course): void
    {
        $order = new Order();

        $order->setCourse($course);

        $this->calculate($order);

        $this->em->persist($order);
        $this->em->flush();

        $this->session->set('order', $order->getId());
    }

    /**
     * @param Order $order
     */
    public function addApplicationFee(Order $order)
    {
        $option = $this->em->getRepository(Option::class)->findOneByCode(Option::CODE_APPLICATION_FEE);

        $order->addOption($option);
    }

    /**
     * @param Order $order
     */
    public function addSendLetter(Order $order)
    {
        $option = $this->em->getRepository(Option::class)->findOneByCode(Option::CODE_SEND_LETTER);

        $order->addOption($option);
    }

    /**
     * @param Order $order
     */
    public function removeSendLetter(Order $order)
    {
        $option = $this->em->getRepository(Option::class)->findOneByCode(Option::CODE_SEND_LETTER);

        $order->removeOption($option);
    }

    /**
     * @param Order  $order
     * @param string $code
     *
     * @return bool
     */
    public function checkCoupon(Order $order, string $code): bool
    {
        $coupon = $this->em->getRepository(Coupon::class)->findOneByCode($code);

        $order->setCoupon(null);

        if (!$coupon instanceof Coupon) {
            return false;
        }

        if ($coupon->getStartAt() >= new \DateTime() || $coupon->getEndAt() <= new \DateTime()) {
            return false;
        }

        if (!$coupon->isEnabled()) {
            return false;
        }

        $order->setCoupon($coupon);

        return true;
    }

    /**
     * @param Course|null $course
     * @param User|null   $user
     * @param string      $status
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCourseByStatus(?Course $course, ?User $user, string $status): int
    {
        return $this->repository->countByStatus($course, $user, $status);
    }

    /**
     * @param Order $order
     *
     * @return PaymentModel
     */
    public function createPayment(Order $order)
    {
        $paymentModel = $this->paymentService->createPayment($order);

        $paymentModelLog = 'Create Payment : ';

        foreach ((array) $paymentModel as $key => $value) {
            $paymentModelLog .= '['.substr($key, 3).' : '.$value.'] ';
        }

        $this->logger->info($paymentModelLog);

        return $paymentModel;
    }

    /**
     * @param Order   $order
     * @param Request $request
     */
    public function addTransaction(Order $order, Request $request): void
    {
        $callBackLog = 'Call Back : ';

        foreach ($request->query->all() as $key => $value) {
            $callBackLog .= '['.$key.' : '.$value.'] ';
        }

        $this->logger->info($callBackLog);
        $this->paymentService->createTransaction($request, $order);

        $this->em->flush();
    }

    public function clear(): void
    {
        if ($this->session->has('order')) {
            $this->session->remove('order');
        }
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function generateConvocation(Order $order): string
    {
        $html = $this->engine->render('pdf/convocation.html.twig', [
            'order' => $order,
        ]);

        $options = [
            'footer-html' => $this->engine->render('pdf/_footer.html.twig', [
                'center' => $order->getCourse()->getCenter(),
                'pager' => false,
            ]),
        ];

        return $this->pdfGenerator->getOutputFromHtml($html, $options);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function generateInvoice(Order $order): string
    {
        $html = $this->engine->render('pdf/invoice.html.twig', [
            'order' => $order,
        ]);

        $options = [
            'header-html' => $this->engine->render('pdf/_header.html.twig'),
            'footer-html' => $this->engine->render('pdf/_footer.html.twig', [
                'center' => $order->getCourse()->getCenter(),
                'pager' => true,
            ]),
        ];

        return $this->pdfGenerator->getOutputFromHtml($html, $options);
    }

    /**
     * @return string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function generateReference(): string
    {
        $parts = [];

        $parts[] = date('Y');
        $parts[] = date('m');

        $order = $this->repository->getLatest(join('', $parts));
        $number = 1;

        if ($order instanceof Order) {
            $number = intval(str_pad(mb_substr($order->getReference(), -5), 5, '', STR_PAD_LEFT));
            ++$number;
        }

        $parts[] = str_pad($number, 5, 0, STR_PAD_LEFT);

        return join('', $parts);
    }
}
