<?php

namespace App\Repository;

use App\Entity\Condition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ConditionRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Condition::class);
    }

    /**
     * @return Condition|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest(): ?Condition
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->andWhere('c.enabled = true')
            ->addOrderBy('c.publishedAt', 'DESC')
            ->setMaxResults(1)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
