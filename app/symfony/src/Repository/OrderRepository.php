<?php

namespace App\Repository;

use App\Entity\Center;
use App\Entity\Course;
use App\Entity\Order;
use App\Entity\Trainee;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OrderRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @param array $filters
     *
     * @return array
     */
    public function search(?array $filters): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->leftJoin('o.trainee', 't');
        $queryBuilder->leftJoin('o.course', 'c');
        $queryBuilder->join('c.place', 'p');

        if (isset($filters['name'])) {
            $queryBuilder->andWhere('t.lastName LIKE :name OR t.firstName LIKE :name');
            $queryBuilder->setParameter('name', '%'.$filters['name'].'%');
        }

        if (isset($filters['reference'])) {
            $queryBuilder->andWhere('t.reference = :reference');
            $queryBuilder->setParameter('reference', $filters['reference']);
        }

        if (isset($filters['status'])) {
            $queryBuilder->andWhere('o.status = :status');
            $queryBuilder->setParameter('status', $filters['status']);
        } else {
            if (isset($filters['role']) && User::ROLE_CSSR === $filters['role']) {
                $queryBuilder->andWhere('o.status IN (:statuses)');
                $queryBuilder->setParameter('statuses', [Order::STATUS_REGISTERED, Order::STATUS_CONFIRMED]);
            } else {
                $queryBuilder->andWhere('o.status != :status');
                $queryBuilder->setParameter('status', Order::STATUS_PENDING);
            }
        }

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

        if (isset($filters['endOn'])) {
            $queryBuilder->andWhere('c.startOn <= :endOn');
            $queryBuilder->setParameter('endOn', $filters['endOn']);
        }

        if (isset($filters['code'])) {
            $queryBuilder->leftJoin('c.center', 'cn');
            $queryBuilder->andWhere('cn.code = :code');
            $queryBuilder->setParameter('code', $filters['code']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Course $course
     *
     * @return array
     */
    public function getRegisteredByCourse(Course $course): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->andWhere('o.course = :course');
        $queryBuilder->andWhere('o.status = :status');
        $queryBuilder->setParameters([
            'course' => $course,
            'status' => Order::STATUS_REGISTERED,
        ]);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Course $course
     *
     * @return array
     */
    public function getConfirmedByCourse(Course $course): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->andWhere('o.course = :course');
        $queryBuilder->andWhere('o.status = :status');
        $queryBuilder->setParameters([
            'course' => $course,
            'status' => Order::STATUS_CONFIRMED,
        ]);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Trainee $trainee
     *
     * @return mixed
     */
    public function getCoursesByTrainee(Trainee $trainee)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->leftJoin('o.trainee', 't');
        $queryBuilder->andWhere('t.driverLicense = :driverLicense');
        $queryBuilder->setParameter('driverLicense', $trainee->getDriverLicense());

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $status
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOrderCountByStatus(string $status): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('COUNT(o.id)')
            ->leftJoin('o.course', 'c')
            ->leftJoin('o.trainee', 't')
            ->andWhere('o.status = :status')
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('c.startOn >= :startOn')
            ->andWhere('t.reference IS NOT NULL')
            ->setParameters([
                'status' => $status,
                'startOn' => new \DateTime(date('Y-m-d')),
            ])
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCurrentConfirmedCount(): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('COUNT(o.id)')
            ->leftJoin('o.course', 'c')
            ->andWhere('o.status = :status')
            ->andWhere('c.startOn >= :startOn')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('status', Order::STATUS_CONFIRMED)
            ->setParameter('startOn', new \DateTime(date('Y-m-d')))
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Course|null $course
     * @param User|null   $user
     * @param string      $status
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByStatus(?Course $course, ?User $user, string $status): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('COUNT(o.id)')
            ->leftJoin('o.course', 'c')
            ->leftJoin('o.trainee', 't')
        ;

        if ($course instanceof Course) {
            $queryBuilder
                ->andWhere('o.course = :course')
                ->andWhere('c.deletedAt IS NULL')
                ->setParameter('course', $course->getId())
            ;
        } else {
            if ($user instanceof User) {
                if (User::ROLE_CSSR !== $user->getRole()) {
                    $queryBuilder
                        ->andWhere('o.course IN (:courses)')
                        ->andWhere('c.deletedAt IS NULL')
                        ->setParameter('courses', $user->getCenter()->getCourses())
                    ;
                } else {
                    $queryBuilder
                        ->andWhere('c.center = :center')
                        ->setParameter('center', $user->getCenter())
                    ;
                }
            }
        }

        if (Order::STATUS_REGISTERED === $status) {
            $queryBuilder
                ->andWhere('c.startOn >= :startOn')
                ->setParameter('startOn', new \DateTime(date('Y-m-d')))
            ;
        }

        $queryBuilder
            ->andWhere('t.reference IS NOT NULL')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getOrderForExport(?array $data): array
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->leftJoin('o.course', 'c');

        if ($data['startOn']) {
            $queryBuilder
                ->andWhere('c.startOn >= :startOn')
                ->setParameter('startOn', $data['startOn'])
            ;
        }

        if ($data['endOn']) {
            $queryBuilder
                ->andWhere('c.startOn <= :endOn')
                ->setParameter('endOn', $data['endOn'])
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string      $status
     * @param Center|null $center
     *
     * @return array
     */
    public function getOrderByStatus(string $status, ?Center $center = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->leftJoin('o.course', 'c')
            ->andWhere('o.status = :status')
            ->andWhere('c.endOn < :endOn')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('status', $status)
            ->setParameter('endOn', new \DateTime(date('Y-m-d')))
        ;

        if ($center instanceof Center) {
            $queryBuilder
                ->andWhere('c.center = :center')
                ->setParameter('center', $center)
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getOrderByRefund(): array
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->leftJoin('o.course', 'c');

        $queryBuilder
            ->andWhere('o.refundAt IS NOT NULL')
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('o.status = :status')
            ->setParameters([
                    'status' => Order::STATUS_REFUNDED,
            ])
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Center|null $center
     *
     * @return array
     */
    public function getOrderForExportBank(?Center $center = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->leftJoin('o.course', 'c');

        $queryBuilder
            ->andWhere('o.status = :status')
            ->andWhere('c.payment IS NULL')
            ->andWhere('c.endOn < CURRENT_DATE() ')
            ->setParameter('status', Order::STATUS_CONFIRMED)
        ;

        if ($center instanceof Center) {
            $queryBuilder
                ->andWhere('c.center = :center')
                ->setParameter('center', $center)
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Center|null $center
     * @param array|null  $data
     *
     * @return array
     */
    public function getOrderForExportCssr(?Center $center, ?array $data): array
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->leftJoin('o.course', 'c');

        if ($data['startOn']) {
            $queryBuilder
                ->andWhere('c.startOn >= :startOn')
                ->setParameter('startOn', $data['startOn'])
            ;
        }

        if ($data['endOn']) {
            $queryBuilder
                ->andWhere('c.startOn <= :endOn')
                ->setParameter('endOn', $data['endOn'])
            ;
        }

        if ($center instanceof Center) {
            $queryBuilder
                ->andWhere('c.center = :center')
                ->setParameter('center', $center)
                ->orderBy('o.id', 'DESC')
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $reference
     *
     * @return Order
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest(string $reference): ?Order
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere('o.reference LIKE :reference')
            ->setParameter('reference', $reference.'%')
            ->setMaxResults(1)
            ->addOrderBy('o.reference', 'DESC')
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
