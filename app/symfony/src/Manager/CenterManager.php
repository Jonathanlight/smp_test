<?php

namespace App\Manager;

use App\Entity\Center;
use App\Entity\Course;
use App\Entity\User;
use App\Repository\CenterRepository;
use App\Services\MessageService;
use App\Services\PaginatorService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\Form;

class CenterManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var CenterRepository
     */
    protected $repository;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var PaginatorService
     */
    protected $paginatorService;

    /**
     * @var TarifManager
     */
    protected $tarifManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CenterRepository       $centerRepository
     * @param PaginatorService       $paginatorService
     * @param UserManager            $userManager
     * @param MessageService         $messageService
     * @param TarifManager           $tarifManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CenterRepository $centerRepository,
        PaginatorService $paginatorService,
        UserManager $userManager,
        MessageService $messageService,
        TarifManager $tarifManager
    ) {
        $this->em = $entityManager;
        $this->userManager = $userManager;
        $this->repository = $centerRepository;
        $this->messageService = $messageService;
        $this->paginatorService = $paginatorService;
        $this->tarifManager = $tarifManager;
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

    /**
     * @param Course $course
     *
     * @return array
     */
    public function collectEnabled(?Course $course = null): array
    {
        return $this->repository->getEnabled($course);
    }

    /**
     * @param Center $center
     * @param User   $user
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(Center $center, User $user): void
    {
        $center->setCode($this->generateCode($center));

        $this->userManager->setPassword($user, $user->getPlainPassword());
        $this->tarifManager->create($center);

        $this->em->persist($center);
        $this->em->flush();

        $this->messageService->addSuccess('message.flash.centerCreate');
    }

    /**
     * @param Center     $center
     * @param Collection $users
     */
    public function update(Center $center, Collection $users): void
    {
        foreach ($users as $user) {
            if (!$user->getPlainPassword()) {
                continue;
            }

            $this->userManager->setPassword($user, $user->getPlainPassword());
        }

        $this->em->flush();
        $this->messageService->addSuccess('message.flash.centerUpdate');
    }

    /**
     * @param Center $center
     */
    public function validationCondition(Center $center): void
    {
        $center->setValidatedCGU(true);

        $this->em->flush();
    }

    /**
     * @param Center $center
     *
     * @return string
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function generateCode(Center $center): string
    {
        $parts = [];

        $parts[] = mb_substr($center->getPostalCode(), 0, 2);
        $parts[] = mb_substr(preg_replace('/\s+/', '', $center->getName()), 0, 8);

        $center = $this->repository->getLatest(join('', $parts));
        $number = 1;

        if ($center instanceof Center) {
            $number = intval(str_pad(mb_substr($center->getCode(), -5), 5, '', STR_PAD_LEFT));
            ++$number;
        }

        $parts[] = str_pad($number, 5, 0, STR_PAD_LEFT);

        return join('', $parts);
    }
}
