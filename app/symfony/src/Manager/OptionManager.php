<?php

namespace App\Manager;

use App\Entity\Option;
use App\Repository\OptionRepository;

class OptionManager
{
    /**
     * @var OptionRepository
     */
    protected $repository;

    /**
     * @param OptionRepository $optionRepository
     */
    public function __construct(OptionRepository $optionRepository)
    {
        $this->repository = $optionRepository;
    }

    /**
     * @param string $code
     *
     * @return Option
     */
    public function getOptionByCode(string $code): ?Option
    {
        return $this->repository->findOneByCode($code);
    }
}
