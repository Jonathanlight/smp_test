<?php

namespace App\Manager;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;

class ParameterManager
{
    /**
     * @var ParameterRepository
     */
    protected $repository;

    /**
     * @param ParameterRepository $parameterRepository
     */
    public function __construct(ParameterRepository $parameterRepository)
    {
        $this->repository = $parameterRepository;
    }

    /**
     * @param string $code
     *
     * @return Parameter
     */
    public function getParameterByCode(string $code): ?Parameter
    {
        return $this->repository->findOneByCode($code);
    }
}
