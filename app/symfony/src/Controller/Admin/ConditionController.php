<?php

namespace App\Controller\Admin;

use App\Entity\Condition;
use App\Repository\CenterRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class ConditionController extends EasyAdminController
{
    /**
     * @var CenterRepository
     */
    private $centerRepository;

    /**
     * @param CenterRepository $centerRepository
     */
    public function __construct(CenterRepository $centerRepository)
    {
        $this->centerRepository = $centerRepository;
    }

    /**
     * @param $entity
     * @throws \Exception
     */
    public function persistEntity($entity)
    {
        if (!$entity instanceof Condition) {
            return;
        }

        if (true === $entity->isEnabled() && null === $entity->getPublishedAt()) {
            $entity->setPublishedAt(new \DateTime());
            foreach ($this->centerRepository->findAll() as $center) {
                $center->setValidatedCGU(false);
            }
        }

        parent::persistEntity($entity);
    }

    /**
     * @param $entity
     * @throws \Exception
     */
    public function updateEntity($entity)
    {
        if (!$entity instanceof Condition) {
            return;
        }

        if (true === $entity->isEnabled() && null === $entity->getPublishedAt()) {
            $entity->setPublishedAt(new \DateTime());
            foreach ($this->centerRepository->findAll() as $center) {
                $center->setValidatedCGU(false);
            }
        }

        parent::updateEntity($entity);
    }
}
