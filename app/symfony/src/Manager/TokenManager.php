<?php

namespace App\Manager;

use App\Entity\Token;
use App\Event\TokenEvent;
use App\Services\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TokenManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TokenService
     */
    protected $tokenService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param TokenService             $tokenService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TokenService $tokenService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $entityManager;
        $this->tokenService = $tokenService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $entity
     *
     * @throws \Exception
     */
    public function create($entity): void
    {
        $token = new Token();
        $dt = new \DateTime();

        $token->setExpiredAt($dt->add(new \DateInterval('PT2H')));
        $token->setValue($this->tokenService->generate());
        $token->setUser($entity);

        $this->em->persist($token);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            TokenEvent::TOKEN_GENERATE,
            new TokenEvent($token)
        );
    }
}
