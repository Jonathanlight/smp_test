<?php

namespace App\Controller\Middle;

use App\Entity\Trainee;
use App\Form\TraineeType;
use App\Manager\TraineeManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TraineeController extends AbstractController
{
    /**
     * @Route(path="/src/trainee/{id}", name="middle_trainee_edit", methods={"GET", "POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\TraineeVoter::TRAINEE_EDIT'), trainee)")
     *
     * @param Request        $request
     * @param Trainee        $trainee
     * @param TraineeManager $traineeManager
     *
     * @return Response
     */
    public function edit(
        Request $request,
        Trainee $trainee,
        TraineeManager $traineeManager
    ): Response {
        $form = $this->createForm(TraineeType::class, $trainee, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $traineeManager->update();

            return $this->redirectToRoute('middle_order', ['id' => $trainee->getOrder()->getId()]);
        }

        return $this->render('middle/trainee/edit.html.twig', [
            'trainee' => $trainee,
            'form' => $form->createView(),
        ]);
    }
}
