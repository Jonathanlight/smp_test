<?php

namespace App\Controller\Middle;

use App\Entity\Center;
use App\Entity\Tarif;
use App\Entity\User;
use App\Form\CenterType;
use App\Manager\CenterManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CenterController extends AbstractController
{
    /**
     * @Route(path="/cssr/center/{id}", name="middle_center_view", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CenterVoter::CENTER_VIEW'), center)")
     *
     * @param Center $center
     *
     * @return Response
     */
    public function view(?Center $center): Response
    {
        return $this->render('middle/center/index.html.twig', [
            'center' => $center,
        ]);
    }

    /**
     * @Route("/cssr/center", name="middle_center_create", methods={"GET","POST"})
     * @Route("/cssr/center/{id}/edit", name="middle_center_edit", methods={"GET","POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CenterVoter::CENTER_EDIT'), center)")
     *
     * @param Request       $request
     * @param CenterManager $centerManager
     * @param Center        $center
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(
        Request $request,
        CenterManager $centerManager,
        ?Center $center
    ): Response {
        $user = $this->getUser();

        if (!$center instanceof Center) {
            $center = new Center();
            $user = new User();
            $user->setCenter($center);
            $center->addUser($user);
        } else {
            $users = $center->getUsers();
            $previousTotalCourse = $center->getTarif()->getTotalCourse();

            foreach ($users as $user) {
                if (User::ROLE_CSSR !== $user->getRole()) {
                    $users->removeElement($user);
                }
            }
        }

        if ($center->getTarif() instanceof Tarif) {
            $tarif = $center->getTarif();
            if ((Tarif::TYPE_DATE === $tarif->getType() && !$tarif->isDiscountDateActive())
                || (Tarif::TYPE_COURSE === $tarif->getType() && !$tarif->getRemainingCourse())) {
                $tarif->setType(null);
                $tarif->setCommissionByDate(null);
                $tarif->setStartOn(null);
                $tarif->setEndOn(null);
                $tarif->setCommissionByCourse(null);
                $tarif->setTotalCourse(null);
            }
        }

        $form = $this->createForm(CenterType::class, $center, [
            'user' => $this->getUser(),
            'center' => $center,
            'is_new' => null === $center->getId(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $center->getId()) {
                $centerManager->create($center, $user);
            } else {
                if ($previousTotalCourse !== $center->getTarif()->getTotalCourse()) {
                    $center->getTarif()->setConfirmedCourse(0);
                }

                $centerManager->update($center, $users);
            }

            return $this->redirectToRoute('middle_center_view', [
                'id' => $center->getId(),
            ]);
        }

        return $this->render('middle/center/edit.html.twig', [
            'center' => $center,
            'form' => $form->createView(),
        ]);
    }
}
