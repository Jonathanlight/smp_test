<?php

namespace App\Repository;

use App\Entity\Tree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TreeRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tree::class);
    }

    /**
     * @param string $code
     *
     * @return Tree
     */
    public function getTreeByCode(string $code): ?Tree
    {
        return $this->findOneByCode($code);
    }
}
