<?php

namespace App\EventSubscriber;

use App\Entity\Letter;
use App\Event\TransferEvent;
use App\Services\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TransferSubscriber implements EventSubscriberInterface
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
     * @param TransferEvent $event
     */
    public function onCreate(TransferEvent $event): void
    {
        $transfer = $event->getTransfer();

        $order = $transfer->getOrder();
        $course = $transfer->getNewCourse();

        $bindings = [
            'reference' => $order->getTrainee()->getReference(),
            'startOn' => $course->getStartOn()->format('d/m/Y'),
            'place' => $course->getPlace()->getCity(),
        ];

        $this->mailer->send(
            Letter::MAIL_TRANSFER_CREATED,
            $order->getTrainee()->getEmail(),
            $bindings
        );

        $bindings += [
            'link' => $this->urlGenerator->generate('middle_course', ['id' => $course->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        if (!$email = $course->getCenter()->getEmail()) {
            foreach ($course->getCenter()->getUsers() as $user) {
                $email = $user->getUsername();
            }
        }

        $this->mailer->send(
            Letter::MAIL_TRANSFER_RECEIVED,
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
            TransferEvent::TRANSFER_CREATED => 'onCreate',
        ];
    }
}
