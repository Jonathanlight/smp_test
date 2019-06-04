<?php

namespace App\Repository;

use App\Entity\Statistical;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StatisticalRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Statistical::class);
    }

    /**
     * @param string      $type
     * @param string|null $date
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findValue(string $type, ?string $date)
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->select('s.value')
            ->andWhere('s.type = :type')
            ->andWhere('s.date = :date')
            ->setParameter('type', $type)
            ->setParameter('date', $date)
            ->setMaxResults(1)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
