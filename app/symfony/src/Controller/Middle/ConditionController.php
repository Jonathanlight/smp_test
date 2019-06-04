<?php

namespace App\Controller\Middle;

use App\Manager\CenterManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConditionController extends AbstractController
{
    /**
     * @Route("/cssr/condition/validate", name="middle_condition_validate", methods={"POST"})
     *
     * @param CenterManager $centerManager
     *
     * @return RedirectResponse
     */
    public function validate(CenterManager $centerManager): RedirectResponse
    {
        $user = $this->getUser();

        $centerManager->validationCondition($user->getCenter());

        return $this->redirectToRoute('dashboard');
    }
}
