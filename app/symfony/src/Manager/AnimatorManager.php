<?php

namespace App\Manager;

use App\Entity\Animator;
use App\Entity\User;
use App\Repository\AnimatorRepository;
use App\Services\MessageService;
use App\Services\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class AnimatorManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PaginatorService
     */
    protected $paginatorService;

    /**
     * @var AnimatorRepository
     */
    protected $repository;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AnimatorRepository     $animatorRepository
     * @param PaginatorService       $paginatorService
     * @param TranslatorInterface    $translator
     * @param MessageService         $messageService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AnimatorRepository $animatorRepository,
        PaginatorService $paginatorService,
        TranslatorInterface $translator,
        MessageService $messageService
    ) {
        $this->em = $entityManager;
        $this->repository = $animatorRepository;
        $this->paginatorService = $paginatorService;
        $this->translator = $translator;
        $this->messageService = $messageService;
    }

    /**
     * @param Animator $animator
     * @param User     $user
     */
    public function create(Animator $animator, User $user): void
    {
        if (!$animator->getCenter()) {
            $animator->setCenter($user->getCenter());
        }

        $this->em->persist($animator);
        $this->em->flush();
        $this->messageService->addSuccess('message.flash.animatorCreate');
    }

    /**
     * @param User       $user
     * @param array|null $filters
     *
     * @return PaginationInterface
     */
    public function collect(User $user, ?array $filters): PaginationInterface
    {
        return $this->paginatorService->paginate(
            $this->repository->search($user, $filters),
            PaginatorService::DEFAULT_LIMIT,
            isset($filters['page']) ? $filters['page'] : PaginatorService::DEFAULT_PAGE
        );
    }

    public function update(): void
    {
        $this->em->flush();
        $this->messageService->addSuccess('message.flash.animatorUpdate');
    }

    /**
     * @param Animator $animator
     */
    public function delete(Animator $animator): void
    {
        if (!$animator instanceof Animator) {
            return;
        }

        $animator->setDeletedAt(new \DateTime());
        $this->em->flush();
    }
}
