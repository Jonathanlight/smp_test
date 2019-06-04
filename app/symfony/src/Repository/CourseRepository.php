<?php

namespace App\Repository;

use App\Entity\Center;
use App\Entity\Course;
use App\Entity\Order;
use App\Form\SearchType;
use App\Manager\CourseManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CourseRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @param array|null $filters
     *
     * @return array
     */
    public function search(?array $filters): array
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->leftJoin('c.center', 'cn');
        $queryBuilder->leftJoin('c.place', 'p');

        if (isset($filters['code'])) {
            $queryBuilder->andWhere('cn.code = :code');
            $queryBuilder->setParameter('code', $filters['code']);
        }

        if (isset($filters['endOn'])) {
            $queryBuilder->andWhere('c.startOn <= :endOn');
            $queryBuilder->setParameter('endOn', $filters['endOn']);
        }

        if (isset($filters['latitude']) && isset($filters['longitude'])) {
            $queryBuilder->andWhere('c.enabled = true');

            if (isset($filters['date'])) {
                $queryBuilder
                    ->andWhere('c.startOn >= :date')
                    ->setParameter('date', $filters['date']->format('Y-m-d'))
                ;
            }

            if (isset($filters['maxPrice'])) {
                $queryBuilder
                    ->andWhere('c.amount <= :maxPrice')
                    ->setParameter('maxPrice', $filters['maxPrice'])
                ;
            }

            if ($filters['dayWeek']) {
                $queryBuilder
                    ->andWhere('WEEKDAY(c.startOn) IN (:dayWeek)')
                    ->setParameter('dayWeek', $filters['dayWeek'])
                ;
            }

            // Calcule la distance d'un point A à un point B
            $distance = '((ACOS(SIN('.
                $filters['latitude'].' * PI() / 180) * SIN(p.latitude * PI() / 180) + COS('.
                $filters['latitude'].' * PI() / 180) * COS(p.latitude * PI() / 180) * COS((p.longitude - '.
                $filters['longitude'].') * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344)';

            $queryBuilder
                ->andWhere($distance.' <= :distance')
                ->setParameter('distance', isset($filters['distance']) ? ($filters['distance'] + 1) : CourseManager::MAX_DISTANCE)
            ;

            $queryBuilder->addOrderBy('IFELSE(c.quantity - c.confirmed > 0, 1, 0)', 'DESC');

            if (isset($filters['sort'])) {
                if (SearchType::FILTER_PROXIMITE === $filters['sort']) {
                    $queryBuilder->addOrderBy($distance, 'ASC');
                    $queryBuilder->addOrderBy('c.amount', 'ASC');
                    $queryBuilder->addOrderBy('c.startOn', 'ASC');
                } elseif (SearchType::FILTER_DATE === $filters['sort']) {
                    $queryBuilder->addOrderBy('c.startOn', 'ASC');
                    $queryBuilder->addOrderBy('c.amount', 'ASC');
                    $queryBuilder->addOrderBy($distance, 'ASC');
                } elseif (SearchType::FILTER_PRIX === $filters['sort']) {
                    $queryBuilder->addOrderBy('c.amount', 'ASC');
                    $queryBuilder->addOrderBy($distance, 'ASC');
                    $queryBuilder->addOrderBy('c.startOn', 'ASC');
                } else {
                    $pertinence = 'IFELSE('.$distance.' >= 0 AND '.$distance." <= 5  AND c.startOn >= :date1day AND c.startOn <= :date14days, '01', 
                                        IFELSE(".$distance.' >= 5 AND '.$distance." <= 10 AND c.startOn >= :date1day AND c.startOn <= :date14days, '02',
                                            IFELSE(".$distance.' >= 0 AND '.$distance." <= 5 AND c.startOn >= :date15days AND c.startOn <= :date30days, '03', 
                                                IFELSE(".$distance.' >= 5 AND '.$distance." <= 10 AND c.startOn >= :date15days AND c.startOn <= :date30days, '04', 
                                                    IFELSE(".$distance.' >= 0 AND '.$distance." <= 5 AND c.startOn >= :date31days AND c.startOn <= :date60days, '05', 
                                                        IFELSE(".$distance.' >= 5 AND '.$distance." <= 10 AND c.startOn >= :date31days AND c.startOn <= :date60days, '06',
                                                            IFELSE(".$distance.' >= 10 AND '.$distance." <= 20 AND c.startOn >= :date1day AND c.startOn <= :date14days, '07',
                                                                IFELSE(".$distance.' >= 10 AND '.$distance." <= 20 AND c.startOn >= :date15days AND c.startOn <= :date30days, '08',
                                                                    IFELSE(".$distance.' >= 10 AND '.$distance." <= 20 AND c.startOn >= :date31days AND c.startOn <= :date60days, '09',
                                                                        IFELSE(".$distance.' >= 21 AND '.$distance." <= 60 AND c.startOn >= :date1day AND c.startOn <= :date14days, '10',
                                                                            IFELSE(".$distance.' >= 21 AND '.$distance." <= 60 AND c.startOn >= :date15days AND c.startOn <= :date30days, '11',
                                                                                IFELSE(".$distance.' >= 21 AND '.$distance." <= 60 AND c.startOn >= :date31days AND c.startOn <= :date60days, '12', '-1')
                                                                            )
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                   )";

                    $queryBuilder
                        ->andWhere($pertinence.' > 0')
                        ->addOrderBy($pertinence, 'ASC')
                        ->addOrderBy('c.amount', 'ASC')
                        ->addOrderBy($distance, 'ASC')
                        ->addOrderBy('c.startOn', 'ASC')
                        ->setParameter('date1day', date('Y-m-d', strtotime('+1 day')))
                        ->setParameter('date14days', date('Y-m-d', strtotime('+14 days')))
                        ->setParameter('date15days', date('Y-m-d', strtotime('+15 days')))
                        ->setParameter('date30days', date('Y-m-d', strtotime('+30 days')))
                        ->setParameter('date31days', date('Y-m-d', strtotime('+31 days')))
                        ->setParameter('date60days', date('Y-m-d', strtotime('+60 days')))
                    ;
                }
            }

            $queryBuilder->setMaxResults(100);
            $queryBuilder->andWhere('c.startOn >= :startOn');
            $queryBuilder->setParameter('startOn', new \DateTime(date('Y-m-d')));
        } else {
            if (!empty($filters['startOn'])) {
                if (empty($filters['pastCourses']) && new \DateTime(date('Y-m-d')) >= $filters['startOn']) {
                    $filters['startOn'] = new \DateTime(date('Y-m-d'));
                }

                $queryBuilder->andWhere('c.startOn >= :startOn');
                $queryBuilder->setParameter('startOn', $filters['startOn']);
            } else {
                if (empty($filters['pastCourses']) || false === $filters['pastCourses']) {
                    $queryBuilder->andWhere('c.startOn >= :startOn');
                    $queryBuilder->setParameter('startOn', new \DateTime(date('Y-m-d')));
                }
            }
        }

        $queryBuilder->andWhere('c.deletedAt IS NULL');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $department
     *
     * @return Course
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCheapestByDepartment(string $department): ?Course
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->leftJoin('c.place', 'p')
            ->andWhere('p.department = :department')
            ->andWhere('c.quantity - c.confirmed > 0')
            ->andWhere('c.enabled = true')
            ->andWhere('c.startOn > :startOn')
            ->addOrderBy('c.amount', 'ASC')
            ->setMaxResults(1)
            ->setParameters([
                'department' => $department,
                'startOn' => new \DateTime(date('Y-m-d')),
            ])
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Center $center
     * @param Course $course
     * @param int    $limit
     * @param bool   $hasSamePrice
     *
     * @return array
     */
    public function collectByCenter(
        Center $center,
        ?Course $course,
        ?int $limit,
        ?bool $hasSamePrice = false
    ): array {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->andWhere('c.center = :center')
            ->andWhere('c.startOn > :startOn')
            ->andWhere('c.enabled = :enabled')
            ->setParameters([
                'startOn' => new \DateTime(date('Y-m-d')),
                'center' => $center,
                'enabled' => true,
            ])
        ;

        if ($course instanceof Course) {
            $queryBuilder
                ->andWhere('c.id != :courseId')
                ->setParameter('courseId', $course->getId())
            ;

            if (true === $hasSamePrice) {
                $queryBuilder
                    ->andWhere('c.amount = :amount')
                    ->setParameter('amount', $course->getAmount())
                ;
            }
        }

        if (null !== $limit) {
            $queryBuilder
                ->leftJoin('c.orders', 'o')
                ->andWhere('o.status = :status')
                ->setMaxResults($limit)
                ->setParameter('status', Order::STATUS_REGISTERED)
                ->addOrderBy('c.startOn', 'ASC')
            ;
        } else {
            $queryBuilder
                ->leftJoin('c.place', 'p')
                ->addOrderBy('p.name', 'ASC')
                ->addOrderBy('c.startOn', 'ASC')
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Center $center
     * @param Course $course
     *
     * @return array
     */
    public function collectByCenterAtSamePrice(Center $center, Course $course): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->andWhere('c.center = :center')
            ->andWhere('c.startOn > :now')
            ->andWhere('c.amount = :amount')
            ->setParameter('center', $center)
            ->setParameter('now', new \DateTime(date('Y-m-d')))
            ->setParameter('amount', $course->getAmount())
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getEnabled(): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder->andWhere('c.deletedAt IS NULL');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $city
     * @param int    $size
     *
     * @return array
     */
    public function getCheaperCourseByCity(string $city, ?int $size = null): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->leftJoin('c.place', 'p')
            ->andWhere('p.city = :city')
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('c.startOn > :startOn')
            ->andWhere('c.enabled = true')
            ->andWhere('c.quantity - c.confirmed > 0')
            ->addOrderBy('c.amount', 'ASC')
            ->setParameters([
                'city' => $city,
                'startOn' => new \DateTime(date('Y-m-d')),
            ])
        ;

        if (null !== $size) {
            $queryBuilder->setMaxResults($size);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param float $longitude
     * @param float $latitude
     *
     * @return array
     */
    public function getCheapiestCoursesByGeolocation(float $longitude, float $latitude): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $distance = '((ACOS(SIN('.
            $latitude.' * PI() / 180) * SIN(p.latitude * PI() / 180) + COS('.
            $latitude.' * PI() / 180) * COS(p.latitude * PI() / 180) * COS((p.longitude - '.
            $longitude.') * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344)';

        $queryBuilder
            ->leftJoin('c.place', 'p')
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('c.startOn > :startOn')
            ->andWhere('c.enabled = true')
            ->andWhere('c.quantity - c.confirmed > 0')
            ->andWhere($distance.' <= :distance')
            ->setParameters([
                'startOn' => new \DateTime(date('Y-m-d')),
                'distance' => CourseManager::MAX_DISTANCE,
            ])
            ->addOrderBy('c.amount', 'ASC')
            ->setMaxResults(CourseManager::MAX_SLIDES)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCurrentActiveCount(): ?int
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->select('COUNT(c.id)')
            ->andWhere('c.enabled = :enabled')
            ->andWhere('c.startOn >= :startOn')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('enabled', true)
            ->setParameter('startOn', new \DateTime(date('Y-m-d')))
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCurrentQuantity(): ?int
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->select('SUM(c.quantity)')
            ->andWhere('c.enabled = :enabled')
            ->andWhere('c.startOn >= :startOn')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('enabled', true)
            ->setParameter('startOn', new \DateTime(date('Y-m-d')))
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return int|null
     */
    public function getCurrentActiveCSSR(): ?int
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->select('COUNT(c.center)')
            ->andWhere('c.startOn >= :startOn')
            ->andWhere('c.enabled = :enabled')
            ->setParameters([
                'enabled' => true,
                'startOn' => new \DateTime(date('Y-m-d')),
            ])
            ->groupBy('c.center')
        ;

        return count($queryBuilder->getQuery()->getResult());
    }
}
