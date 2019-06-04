<?php

namespace App\Services;

use App\Entity\Delivery;
use App\Entity\Letter;
use App\Entity\Parameter;
use App\Manager\LetterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;

class MailerService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var LetterManager
     */
    private $letterManager;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LetterManager          $letterManager
     * @param EngineInterface        $engine
     * @param \Swift_Mailer          $mailer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LetterManager $letterManager,
        EngineInterface $engine,
        \Swift_Mailer $mailer
    ) {
        $this->em = $entityManager;
        $this->letterManager = $letterManager;
        $this->engine = $engine;
        $this->mailer = $mailer;
    }

    /**
     * @param string $code
     * @param string $email
     * @param array  $bindings
     * @param array  $attachments
     */
    public function send(
        string $code,
        ?string $email,
        array $bindings,
        ?array $attachments = null
    ): void {
        $letter = $this->letterManager->loadByCode($code);

        if (Letter::MAIL_ORDER_PAID === $code && null === $email) {
            $email = Parameter::EMAIL_DEFAULT;
        }

        if (!$letter instanceof Letter || null === $email) {
            return;
        }

        $message = new \Swift_Message();

        $message
            ->setSubject($this->setBinding($letter->getSubject(), $bindings))
            ->setFrom(['noreply@plusdepoints.fr' => 'Plus de points'])
            ->setTo($email)
            ->setBody($this->engine->render('mail.html.twig', [
                'content' => $this->setBinding($letter->getContent(), $bindings),
            ]), 'text/html')
        ;

        if (Letter::MAIL_ORDER_CONFIRMED === $code && Parameter::EMAIL_DEFAULT !== $email) {
            $message->setBcc(Parameter::EMAIL_DEFAULT);
        }

        if (!empty($attachments)) {
            foreach ($attachments as $data) {
                $attachment = new \Swift_Attachment(
                    $data['pdf'],
                    $data['filename'],
                    'application/pdf'
                );

                $message->attach($attachment);
            }
        }

        if ($this->mailer->send($message)) {
            $delivery = new Delivery();

            $delivery->setEmail($email);
            $delivery->setLetter($letter);
            $delivery->setSentAt(new \DateTime());

            $this->em->persist($delivery);
            $this->em->flush();
        }
    }

    /**
     * @param string $content
     * @param array  $bindings
     *
     * @return string
     */
    private function setBinding(string $content, array $bindings): string
    {
        return \str_replace(
            array_map(function ($binding) {
                return '%'.$binding.'%';
            }, array_keys($bindings)),
            array_values($bindings),
            $content
        );
    }
}
