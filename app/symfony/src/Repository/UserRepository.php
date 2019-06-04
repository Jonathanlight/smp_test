<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param array|null $filters
     *
     * @return array
     */
    public function search(?array $filters): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if (isset($filters['center'])) {
            $queryBuilder->andWhere('u.center = :center');
            $queryBuilder->setParameter('center', $filters['center']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastCssr()
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.role = :role');
        $qb->setParameter('role', 'ROLE_CSSR');
        $qb->orderBy('u.id', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $code
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCSSRByCenterCode(string $code)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.center', 'c');
        $qb->andWhere('c.code = :code');
        $qb->setParameter('code', $code);

        return $qb->getQuery()->getSingleResult();
    }
}
