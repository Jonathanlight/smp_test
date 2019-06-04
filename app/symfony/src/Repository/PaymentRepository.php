<?php

namespace App\Repository;

use App\Entity\Center;
use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @param Center     $center
     * @param array|null $filters
     *
     * @return array
     */
    public function collect(Center $center, ?array $filters): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->andWhere('p.center = :center')
            ->orderBy('p.generatedAt', 'DESC')
            ->setParameter('center', $center)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $reference
     *
     * @return Payment
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest(string $reference): ?Payment
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->andWhere('p.reference LIKE :reference')
            ->setParameter('reference', $reference.'%')
            ->setMaxResults(1)
            ->addOrderBy('p.reference', 'DESC')
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
