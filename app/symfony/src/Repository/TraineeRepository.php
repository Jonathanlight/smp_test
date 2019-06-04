<?php

namespace App\Repository;

use App\Entity\Trainee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TraineeRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Trainee::class);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function search(?array $data): array
    {
        $queryBuilder = $this->createQueryBuilder('a');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $reference
     *
     * @return Trainee|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest(string $reference): ?Trainee
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder
            ->andWhere('t.reference LIKE :reference')
            ->setParameter('reference', $reference.'%')
            ->setMaxResults(1)
            ->addOrderBy('t.reference', 'DESC')
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
