<?php

namespace App\Manager;

use App\Entity\Center;
use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Repository\OrderRepository;
use App\Services\MessageService;
use App\Services\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormInterface;

class CourseManager
{
    const BIGGEST_CITIES = [
        'Paris',
        'Marseille',
        'Lille',
        'Lyon',
        'Rennes',
        'Strasbourg',
    ];

    const MAX_DISTANCE = 60;
    const MAX_SLIDES = 6;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var CourseRepository
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
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * CourseManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseRepository       $courseRepository
     * @param PaginatorService       $paginatorService
     * @param MessageService         $messageService
     * @param OrderRepository        $orderRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CourseRepository $courseRepository,
        PaginatorService $paginatorService,
        MessageService $messageService,
        OrderRepository $orderRepository
    ) {
        $this->em = $entityManager;
        $this->repository = $courseRepository;
        $this->messageService = $messageService;
        $this->paginatorService = $paginatorService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param array|null $filters
     *
     * @return PaginationInterface
     */
    public function collect(?array $filters): PaginationInterface
    {
        return $this->paginatorService->paginate(
            $this->repository->search($filters, null),
            PaginatorService::DEFAULT_LIMIT,
            isset($filters['page']) ? $filters['page'] : PaginatorService::DEFAULT_PAGE
        );
    }

    /**
     * @return mixed
     */
    public function collectEnabled()
    {
        return $this->repository->getEnabled();
    }

    /**
     * @param Course $course
     * @param float  $amount
     */
    public function update(Course $course, ?float $amount): void
    {
        if (null === $course->getOriginalPrice() && $amount !== $course->getAmount()) {
            $course->setOriginalPrice($amount);
        }

        $this->em->flush();
        $this->messageService->addSuccess('message.flash.courseUpdate');
    }

    /**
     * @param Course $course
     */
    public function updateComment(Course $course): void
    {
        $this->em->flush();
    }

    /**
     * @param Course $course
     */
    public function create(Course $course): void
    {
        $center = $course->getPlace()->getCenter();

        if (!$center->getBic() || !$center->getIban()) {
            $course->setEnabled(false);
        }

        if ($center instanceof Center) {
            $course->setCenter($center);
        }

        $this->em->persist($course);
        $this->em->flush();

        $this->messageService->addSuccess('message.flash.courseCreate');
    }

    /**
     * @param Course $course
     */
    public function enable(Course $course): void
    {
        $course->setEnabled(true);
        $this->em->flush();
    }

    /**
     * @param Center      $center
     * @param Course|null $course
     * @param int         $limit
     * @param bool        $hasSamePrice
     *
     * @return array
     */
    public function collectByCenter(
        Center $center,
        ?Course $course = null,
        ?int $limit = null,
        ?bool $hasSamePrice
    ): array {
        return $this->repository->collectByCenter($center, $course, $limit, $hasSamePrice);
    }

    /**
     * @param Course $course
     */
    public function disable(Course $course): void
    {
        $course->setEnabled(false);
        $this->em->flush();
    }

    /**
     * @param Course $course
     */
    public function delete(Course $course): void
    {
        $this->em->remove($course);
        $this->em->flush();
        $this->messageService->addSuccess('message.flash.courseDelete');
    }

    /**@
     * @param float $latitude
     * @param float $longitude
     * @param bool $geolocated
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCarouselCourses(float $latitude, float $longitude, bool $geolocated): array
    {
        if (true === $geolocated) {
            return $this->getCheapestCoursesAround($longitude, $latitude);
        }

        return $this->getCheapestCourses(1);
    }

    /**
     * @param int $size
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCheapestCourses(?int $size = null): array
    {
        $courses = [];

        foreach (self::BIGGEST_CITIES as $city) {
            foreach ($this->repository->getCheaperCourseByCity($city, $size) as $course) {
                $courses[] = $course;
            }
        }

        return $courses;
    }

    /**
     * @param $longitude
     * @param $latitude
     *
     * @return array
     */
    private function getCheapestCoursesAround(float $longitude, float $latitude): array
    {
        return $this->repository->getCheapiestCoursesByGeolocation($longitude, $latitude);
    }

    /**
     * @param FormInterface $form
     * @param mixed         $cheapest
     *
     * @return array
     */
    public function search(FormInterface $form, $cheapest = null): array
    {
        return $this->repository->search($form->getData(), $cheapest);
    }
}
