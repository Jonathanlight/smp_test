<?php

namespace App\Repository;

use App\Entity\Center;
use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CenterRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Center::class);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function search(?array $data): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $code
     *
     * @return Center|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest(string $code): ?Center
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->andWhere('c.code LIKE :code')
            ->setParameter('code', $code.'%')
            ->setMaxResults(1)
            ->addOrderBy('c.code', 'DESC')
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Course $course
     *
     * @return array
     */
    public function getEnabled(?Course $course = null): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->andWhere('c.deletedAt IS NULL')
            ->orderBy('c.code', 'ASC')
        ;

        if ($course instanceof Course) {
            $queryBuilder
                ->leftJoin('c.courses', 'co')
                ->andWhere('co.startOn >= :startOn')
                ->andWhere('co.amount = :amount')
                ->andWhere('co.id != :courseId')
                ->setParameters([
                    'startOn' => new \DateTime(),
                    'amount' => $course->getAmount(),
                    'courseId' => $course->getId(),
                ])
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
