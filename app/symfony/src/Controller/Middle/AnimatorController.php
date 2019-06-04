<?php

namespace App\Controller\Middle;

use App\Entity\Animator;
use App\Form\AnimatorType;
use App\Form\Search\AnimatorSearchType;
use App\Manager\AnimatorManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class AnimatorController extends AbstractController
{
    /**
     * @Route("/cssr/animators", name="middle_animators", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\AnimatorVoter::ANIMATOR_LIST'))")
     *
     * @param Request         $request
     * @param AnimatorManager $animatorManager
     *
     * @return Response
     */
    public function list(
        Request $request,
        AnimatorManager $animatorManager
    ): Response {
        $form = $this->createForm(AnimatorSearchType::class, null);

        $form->handleRequest($request);
        $filters = $form->getData();

        return $this->render('middle/animator/list.html.twig', [
            'animators' => $animatorManager->collect($this->getUser(), $filters),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/animator", name="middle_animator_create", methods={"GET","POST"})
     * @Route("/cssr/animator/{id}/edit", name="middle_animator_edit", methods={"GET","POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\AnimatorVoter::ANIMATOR_EDIT'))")
     *
     * @param Request         $request
     * @param Animator|null   $animator
     * @param AnimatorManager $animatorManager
     *
     * @return Response
     */
    public function edit(
        Request $request,
        ?Animator $animator,
        AnimatorManager $animatorManager
    ): Response {
        if (!$animator instanceof Animator) {
            $animator = new Animator();
        }

        $form = $this->createForm(AnimatorType::class, $animator, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($animator->getId()) {
                $animatorManager->update();
            } else {
                $animatorManager->create($animator, $this->getUser());
            }

            return $this->redirectToRoute('middle_animators');
        }

        return $this->render('middle/animator/edit.html.twig', [
            'animator' => $animator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/animator/{id}", name="animator_delete", methods={"DELETE"})
     *
     * @param Animator            $animator
     * @param AnimatorManager     $animatorManager
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @Security("is_granted(constant('\\App\\Security\\Voter\\AnimatorVoter::ANIMATOR_DELETE'))")
     */
    public function delete(
        Animator $animator,
        AnimatorManager $animatorManager,
        TranslatorInterface $translator
    ): Response {
        $animatorManager->delete($animator);

        return new JsonResponse($translator->trans('message.flash.animatorDelete'));
    }
}
