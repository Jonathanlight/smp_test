<?php

namespace App\Manager;

use App\Repository\FaqRepository;
use Doctrine\ORM\EntityManagerInterface;

class FaqManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var FaqRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param FaqRepository          $faqRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FaqRepository $faqRepository
    ) {
        $this->em = $entityManager;
        $this->repository = $faqRepository;
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        return $this->repository->findAll();
    }
}
