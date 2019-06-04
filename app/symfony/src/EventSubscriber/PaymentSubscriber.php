<?php

namespace App\EventSubscriber;

use App\Entity\Letter;
use App\Event\PaymentEvent;
use App\Services\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerService
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @param MailerService         $mailerService
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
      MailerService $mailerService,
      UrlGeneratorInterface $urlGenerator
    ) {
        $this->mailer = $mailerService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param PaymentEvent $event
     */
    public function onReceive(PaymentEvent $event): void
    {
        $payment = $event->getPayment();

        $bindings = [
            'reference' => $payment->getCenter()->getCode(),
            'link' => $this->urlGenerator->generate('middle_payments', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        if (!$email = $payment->getCenter()->getEmail()) {
            foreach ($payment->getCenter()->getUsers() as $user) {
                $email = $user->getUsername();
            }
        }

        $this->mailer->send(
            Letter::MAIL_PAYMENT_RECEIVED,
            $email,
            $bindings
        );
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PaymentEvent::PAYMENT_RECEIVED => 'onReceive',
        ];
    }
}
