<?php

namespace App\Controller\Middle;

use App\Entity\Place;
use App\Entity\User;
use App\Form\PlaceType;
use App\Form\Search\PlaceSearchType;
use App\Manager\PlaceManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class PlaceController extends AbstractController
{
    /**
     * @Route(path="/cssr/places", name="middle_places", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\PlaceVoter::PLACE_VIEW'))")
     *
     * @param Request      $request
     * @param PlaceManager $placeManager
     *
     * @return Response
     */
    public function list(
        Request $request,
        PlaceManager $placeManager
    ): Response {
        $form = $this->createForm(PlaceSearchType::class);
        $form->handleRequest($request);

        return $this->render('middle/place/list.html.twig', [
            'places' => $placeManager->collect($this->getUser(), $form->getData()),
        ]);
    }

    /**
     * @Route(path="/cssr/place", name="middle_place_create", methods={"GET","POST"})
     * @Route(path="/cssr/place/{id}/edit", name="middle_place_edit", methods={"GET","POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\PlaceVoter::PLACE_EDIT'), user, place)")
     *
     * @param Request      $request
     * @param PlaceManager $placeManager
     * @param Place|null   $place
     *
     * @return Response
     */
    public function edit(
        Request $request,
        PlaceManager $placeManager,
        ?Place $place
    ): Response {
        if (!$place instanceof Place) {
            $place = new Place();
        }

        $form = $this->createForm(PlaceType::class, $place, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($place->getId()) {
                $placeManager->update();
            } else {
                $placeManager->create($place, $this->getUser());
            }

            return $this->redirectToRoute('middle_places');
        }

        return $this->render('middle/place/edit.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/place/{id}", name="middle_place_delete", methods={"DELETE"})
     *
     * @param Place               $place
     * @param PlaceManager        $placeManager
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function delete(
        Place $place,
        PlaceManager $placeManager,
        TranslatorInterface $translator
    ): Response {
        $placeManager->delete($place);

        return new JsonResponse($translator->trans('message.flash.placeDelete'));
    }
}
