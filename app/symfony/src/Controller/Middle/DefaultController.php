<?php

namespace App\Controller\Middle;

use App\Entity\User;
use App\Manager\CourseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route(path="/cssr", name="dashboard", methods={"GET"})
     *
     * @param CourseManager $courseManager
     *
     * @return Response
     */
    public function dashboard(UrlGeneratorInterface $urlG, CourseManager $courseManager): Response
    {
        $user = $this->getUser();
        $courses = null;

        if (User::ROLE_CSSR === $user->getRole()) {
            $courses = $courseManager->collectByCenter(
                $this->getUser()->getCenter(),
                null,
                2,
                false
            );
        }

        return $this->render('middle/default/index.html.twig', [
            'courses' => $courses,
        ]);
    }
}
