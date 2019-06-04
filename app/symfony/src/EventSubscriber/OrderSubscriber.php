<?php

namespace App\EventSubscriber;

use App\Entity\Letter;
use App\Entity\Parameter;
use App\Event\OrderEvent;
use App\Manager\OrderManager;
use App\Manager\ParameterManager;
use App\Services\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerService
     */
    protected $mailer;

    /**
     * @var OrderManager
     */
    protected $orderManager;

    /**
     * @var ParameterManager
     */
    protected $parameterManager;

    /**
     * @param MailerService    $mailerService
     * @param OrderManager     $orderManager
     * @param ParameterManager $parameterManager
     */
    public function __construct(
        MailerService $mailerService,
        OrderManager $orderManager,
        ParameterManager $parameterManager
    ) {
        $this->mailer = $mailerService;
        $this->orderManager = $orderManager;
        $this->parameterManager = $parameterManager;
    }

    /**
     * @param OrderEvent $event
     */
    public function onPaid(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $bindings = [
            'reference' => $order->getTrainee()->getReference(),
            'startOn' => $order->getCourse()->getStartOn()->format('d/m/Y'),
            'place' => $order->getCourse()->getPlace()->getCity(),
        ];

        $attachments = [
            [
                'pdf' => $this->orderManager->generateInvoice($order),
                'filename' => 'facture.pdf',
            ],
        ];

        $this->mailer->send(
            Letter::MAIL_ORDER_PAID,
            $order->getTrainee()->getEmail(),
            $bindings,
            $attachments
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function onConfirm(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $bindings = [
            'reference' => $order->getTrainee()->getReference(),
        ];

        $attachments = [
            [
                'pdf' => $this->orderManager->generateConvocation($order),
                'filename' => 'convocation.pdf',
            ],
        ];

        $this->mailer->send(
            Letter::MAIL_ORDER_CONFIRMED,
            $order->getTrainee()->getEmail() ?: Parameter::EMAIL_DEFAULT,
            $bindings,
            $attachments
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function onRefund(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $bindings = [
            'reference' => $order->getTrainee()->getReference(),
        ];

        $this->mailer->send(
            Letter::MAIL_ORDER_REFUNDED,
            $order->getTrainee()->getEmail(),
            $bindings
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function onWait(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $bindings = [
            'email' => $this->parameterManager->getParameterByCode(Parameter::CODE_EMAIL)->getValue(),
            'phone' => $this->parameterManager->getParameterByCode(Parameter::CODE_PHONE)->getValue(),
        ];

        $this->mailer->send(
            Letter::MAIL_ORDER_WAITING,
            $order->getTrainee()->getEmail(),
            $bindings
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function onCancel(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $bindings = [
            'email' => $this->parameterManager->getParameterByCode(Parameter::CODE_EMAIL)->getValue(),
            'phone' => $this->parameterManager->getParameterByCode(Parameter::CODE_PHONE)->getValue(),
            'reference' => $order->getTrainee()->getReference(),
            'startOn' => $order->getCourse()->getStartOn()->format('d/m/Y'),
            'place' => $order->getCourse()->getPlace()->getCity(),
        ];

        $this->mailer->send(
            Letter::MAIL_ORDER_CANCELLED,
            $order->getTrainee()->getEmail(),
            $bindings
        );
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvent::ORDER_PAID => 'onPaid',
            OrderEvent::ORDER_CONFIRMED => 'onConfirm',
            OrderEvent::ORDER_REFUNDED => 'onRefund',
            OrderEvent::ORDER_WAITING => 'onWait',
            OrderEvent::ORDER_CANCELLED => 'onCancel',
        ];
    }
}
