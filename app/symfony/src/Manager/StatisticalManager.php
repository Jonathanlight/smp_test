<?php

namespace App\Manager;

use App\Entity\Statistical;
use App\Repository\StatisticalRepository;
use Doctrine\ORM\EntityManagerInterface;

class StatisticalManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var StatisticalRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StatisticalRepository  $statisticalRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StatisticalRepository $statisticalRepository
    ) {
        $this->em = $entityManager;
        $this->repository = $statisticalRepository;
    }

    /**
     * @param string $type
     * @param int    $value
     */
    public function create(string $type, int $value): void
    {
        $statistical = new Statistical();
        $statistical->setValue($value);
        $statistical->setType($type);
        $statistical->setDate(new \DateTime('yesterday'));
        $this->em->persist($statistical);
        $this->em->flush();
    }

    /**
     * @param string $type
     * @param string $date
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function find(string $type, string $date): ?array
    {
        return $this->repository->findValue($type, $date);
    }
}
