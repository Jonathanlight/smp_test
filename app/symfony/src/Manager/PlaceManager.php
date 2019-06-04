<?php

namespace App\Manager;

use App\Entity\Course;
use App\Entity\Place;
use App\Entity\User;
use App\Repository\CourseRepository;
use App\Repository\PlaceRepository;
use App\Services\MessageService;
use App\Services\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class PlaceManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PlaceRepository
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
     * @var CourseRepository
     */
    protected $courseRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PlaceRepository        $placeRepository
     * @param PaginatorService       $paginatorService
     * @param MessageService         $messageService
     * @param CourseRepository       $courseRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PlaceRepository $placeRepository,
        PaginatorService $paginatorService,
        MessageService $messageService,
        CourseRepository $courseRepository
    ) {
        $this->em = $entityManager;
        $this->repository = $placeRepository;
        $this->messageService = $messageService;
        $this->paginatorService = $paginatorService;
        $this->courseRepository = $courseRepository;
    }

    /**
     * @param User  $user
     * @param array $filters
     *
     * @return PaginationInterface
     */
    public function collect(User $user, ?array $filters): PaginationInterface
    {
        return $this->paginatorService->paginate(
            $this->repository->search($user),
            PaginatorService::DEFAULT_LIMIT,
            isset($filters['page']) ? $filters['page'] : PaginatorService::DEFAULT_PAGE
        );
    }

    public function update(): void
    {
        $this->em->flush();
        $this->messageService->addSuccess('message.flash.placeUpdate');
    }

    /**
     * @param Place $place
     * @param User  $user
     */
    public function create(Place $place, User $user): void
    {
        if (!$place->getCenter()) {
            $place->setCenter($user->getCenter());
        }

        $this->em->persist($place);
        $this->em->flush();
        $this->messageService->addSuccess('message.flash.placeCreate');
    }

    /**
     * @param Place $place
     *
     * @return float
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMinPriceByDepartment(Place $place): ?float
    {
        $course = $this->courseRepository->getCheapestByDepartment($place->getDepartment(), 1);

        if (!$course instanceof Course) {
            return null;
        }

        return $course->getAmount();
    }

    /**
     * @param Place $place
     */
    public function delete(Place $place): void
    {
        if (!$place instanceof Place) {
            return;
        }

        $place->setDeletedAt(new \DateTime());
        $this->em->flush();
    }
}
