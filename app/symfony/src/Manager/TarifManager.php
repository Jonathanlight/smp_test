<?php

namespace App\Manager;

use App\Entity\Center;
use App\Entity\Parameter;
use App\Entity\Tarif;
use Doctrine\ORM\EntityManagerInterface;

class TarifManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ParameterManager
     */
    protected $parameterManager;

    /**
     * @param EntityManagerInterface $em
     * @param ParameterManager       $parameterManager
     */
    public function __construct(
        EntityManagerInterface $em,
        ParameterManager $parameterManager
    ) {
        $this->em = $em;
        $this->parameterManager = $parameterManager;
    }

    /**
     * @param Center $center
     */
    public function create(Center $center): void
    {
        $tarif = new Tarif();

        $tarif->setCommission($this->parameterManager->getParameterByCode(Parameter::CODE_COMMISSION)->getValue());

        $center->setTarif($tarif);

        $this->em->persist($tarif);
        $this->em->flush();
    }
}
