<?php

namespace App\EventSubscriber;

use App\Entity\Letter;
use App\Event\TokenEvent;
use App\Services\MailerService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TokenSubscriber implements EventSubscriberInterface
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
     * @param TokenEvent $tokenEvent
     */
    public function onTokenGenerate(TokenEvent $tokenEvent): void
    {
        $token = $tokenEvent->getToken();

        $bindings = [
            'link' => $this->urlGenerator->generate('user_reset', [
                'token' => $token->getValue(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $this->mailer->send(Letter::MAIL_USER_RESET, $token->getUser()->getUsername(), $bindings);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TokenEvent::TOKEN_GENERATE => 'onTokenGenerate',
        ];
    }
}
