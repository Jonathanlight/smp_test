<?php

namespace App\Manager;

use App\Entity\Trainee;
use App\Repository\TraineeRepository;
use App\Services\MessageService;
use App\Services\PaginatorService;
use App\Services\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\Form;

class TraineeManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TraineeRepository
     */
    protected $repository;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var PaginatorService
     */
    protected $paginatorService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TraineeRepository      $traineeRepository
     * @param PaginatorService       $paginatorService
     * @param MessageService         $messageService
     * @param TokenService           $tokenService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TraineeRepository $traineeRepository,
        PaginatorService $paginatorService,
        MessageService $messageService,
        TokenService $tokenService
    ) {
        $this->em = $entityManager;
        $this->repository = $traineeRepository;
        $this->messageService = $messageService;
        $this->paginatorService = $paginatorService;
        $this->tokenService = $tokenService;
    }

    /**
     * @param Form $form
     *
     * @return PaginationInterface
     */
    public function collect(Form $form): PaginationInterface
    {
        return $this->paginatorService->paginate(
            $this->repository->search($form->getData()),
            PaginatorService::DEFAULT_LIMIT,
            PaginatorService::DEFAULT_PAGE
        );
    }

    public function update(): void
    {
        $this->em->flush();
        $this->messageService->addSuccess('message.flash.placeUpdate');
    }

    /**
     * @param Trainee $trainee
     */
    public function create(Trainee $trainee): void
    {
        $this->em->persist($trainee);
        $this->em->flush();

        $this->messageService->addSuccess('message.flash.traineeCreate');
    }

    /**
     * @param Trainee $trainee
     */
    public function delete(Trainee $trainee): void
    {
        if (!$trainee instanceof Trainee) {
            return;
        }

        $trainee->setDeletedAt(new \DateTime());
        $this->em->flush();
    }

    /**
     * @param Trainee $trainee
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateReference(Trainee $trainee): void
    {
        $trainee->setReference($this->generateReference());
    }

    /**
     * @return string
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function generateReference(): string
    {
        $parts = [];

        $parts[] = 'S';
        $parts[] = date('Y');
        $parts[] = date('m');
        $parts[] = date('d');

        $trainee = $this->repository->getLatest(join('', $parts));
        $number = 1;

        if ($trainee instanceof Trainee) {
            $number = intval(str_pad(mb_substr($trainee->getReference(), -5), 5, '', STR_PAD_LEFT));
            ++$number;
        }

        $parts[] = str_pad($number, 5, 0, STR_PAD_LEFT);

        return join('', $parts);
    }
}
