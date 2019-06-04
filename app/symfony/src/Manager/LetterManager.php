<?php

namespace App\Manager;

use App\Entity\Letter;
use Doctrine\ORM\EntityManagerInterface;

class LetterManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Letter::class);
    }

    /**
     * @param string $code
     *
     * @return Letter|null
     */
    public function loadByCode(string $code): ?Letter
    {
        return $this->repository->findOneBy(['code' => $code]);
    }
}
