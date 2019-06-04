<?php

namespace App\Twig;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CourseExtension extends AbstractExtension
{
    /**
     * @var CourseRepository
     */
    protected $courseRepository;

    /**
     * @param CourseRepository $courseRepository
     */
    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getMinPriceByDepartment', [$this, 'getMinPriceByDepartment']),
            new TwigFunction('getDistance', [$this, 'getDistance']),
        ];
    }

    /**
     * @param string $departement
     *
     * @return float
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMinPriceByDepartment(?string $departement): ?float
    {
        $course = $this->courseRepository->getCheapestByDepartment($departement);

        if (!$course instanceof Course) {
            return null;
        }

        return $course->getAmount();
    }

    /**
     * @param Course $course
     * @param float  $latitude
     * @param float  $longitude
     *
     * @return float
     */
    public function getDistance(Course $course, ?float $latitude, ?float $longitude): ?float
    {
        if (null === $latitude || null === $longitude) {
            return null;
        }

        return round(rad2deg(acos((sin(deg2rad($latitude)) * sin(deg2rad($course->getPlace()->getLatitude()))) + (cos(deg2rad($latitude)) * cos(deg2rad($course->getPlace()->getLatitude())) * cos(deg2rad($longitude - $course->getPlace()->getLongitude()))))) * 111.13384, 2);
    }
}
