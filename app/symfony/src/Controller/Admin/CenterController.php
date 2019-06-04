<?php

namespace App\Controller\Admin;

use App\Entity\Center;
use App\Manager\CenterManager;
use App\Manager\TarifManager;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class CenterController extends EasyAdminController
{
    /**
     * @var CenterManager
     */
    private $manager;

    /**
     * @var TarifManager
     */
    private $tarifManager;

    /**
     * @param CenterManager $centerManager
     * @param TarifManager  $tarifManager
     */
    public function __construct(
        CenterManager $centerManager,
        TarifManager $tarifManager
    ) {
        $this->manager = $centerManager;
        $this->tarifManager = $tarifManager;
    }

    /**
     * @param object $entity
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function persistEntity($entity)
    {
        if (!$entity instanceof Center) {
            return;
        }

        $entity->setCode($this->manager->generateCode($entity));
        $this->tarifManager->create($entity);

        parent::persistEntity($entity);
    }
}
