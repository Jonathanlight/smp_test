<?php

namespace App\Manager;

use App\Repository\TestimonialRepository;
use Doctrine\ORM\EntityManagerInterface;

class TestimonialManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TestimonialRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TestimonialRepository  $testimonialRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TestimonialRepository $testimonialRepository
    ) {
        $this->em = $entityManager;
        $this->repository = $testimonialRepository;
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        return $this->repository->findAll();
    }
}
