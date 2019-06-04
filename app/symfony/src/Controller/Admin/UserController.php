<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Services\PasswordService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class UserController extends EasyAdminController
{
    /**
     * @var PasswordService
     */
    private $passwordService;

    /**
     * @param PasswordService $passwordService
     */
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @param object $entity
     */
    protected function updateEntity($entity)
    {
        if (!$entity instanceof User) {
            return;
        }

        if ($entity->getPlainPassword()) {
            $entity->setPassword($this->passwordService->encode($entity, $entity->getPlainPassword()));
        }

        parent::updateEntity($entity);
    }

    /**
     * @param object $entity
     */
    protected function persistEntity($entity)
    {
        if (!$entity instanceof User) {
            return;
        }

        if ($entity->getPlainPassword()) {
            $entity->setPassword($this->passwordService->encode($entity, $entity->getPlainPassword()));
        }

        parent::persistEntity($entity);
    }
}
