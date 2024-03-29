<?php

namespace App\Repository;

use App\Entity\Parameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ParameterRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Parameter::class);
    }
}
