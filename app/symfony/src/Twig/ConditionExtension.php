<?php

namespace App\Twig;

use App\Entity\Condition;
use App\Repository\ConditionRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConditionExtension extends AbstractExtension
{
    /**
     * @var ConditionRepository
     */
    protected $repository;

    /**
     * @param ConditionRepository $conditionRepository
     */
    public function __construct(ConditionRepository $conditionRepository)
    {
        $this->repository = $conditionRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getLatestCondition', [$this, 'getLatest']),
        ];
    }

    /**
     * @return Condition
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest(): ?Condition
    {
        return $this->repository->getLatest();
    }
}
