<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class CourseController extends EasyAdminController
{
    /**
     * @param object $entity
     */
    protected function persistEntity($entity)
    {
        if (!$entity instanceof Course) {
            return;
        }

        if (!$entity->getCenter()) {
            $entity->setCenter($entity->getPlace()->getCenter());
        }

        parent::persistEntity($entity);
    }
}
