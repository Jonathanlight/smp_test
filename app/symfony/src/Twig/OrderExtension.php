<?php

namespace App\Twig;

use App\Entity\Course;
use App\Manager\OrderManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OrderExtension extends AbstractExtension
{
    /**
     * @var OrderManager
     */
    protected $orderManager;

    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @param OrderManager          $orderManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        OrderManager $orderManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->orderManager = $orderManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('countRegistered', [$this, 'countCourseByStatus']),
            new TwigFunction('countConfirmed', [$this, 'countCourseByStatus']),
            new TwigFunction('countCancelled', [$this, 'countCourseByStatus']),
            new TwigFunction('countWaiting', [$this, 'countCourseByStatus']),
            new TwigFunction('countRefunded', [$this, 'countCourseByStatus']),
            new TwigFunction('countAllRegistered', [$this, 'countCourseByStatus']),
            new TwigFunction('countAllConfirmed', [$this, 'countCourseByStatus']),
        ];
    }

    /**
     * @param Course|null $course
     * @param string|null $status
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCourseByStatus(?Course $course, ?string $status)
    {
        return $this->orderManager->countCourseByStatus(
            $course,
            $this->tokenStorage->getToken()->getUser(),
            $status
        );
    }
}
