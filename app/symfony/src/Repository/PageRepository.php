<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PageRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->andWhere('p.slug IS NOT NULL')
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
