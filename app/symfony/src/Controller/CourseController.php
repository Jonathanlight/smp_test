<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\SearchType;
use App\Manager\CourseManager;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseController extends AbstractController
{
    /**
     * @Route(path="/search", name="course_search", methods={"GET"})
     *
     * @param CourseManager    $courseManager
     * @param CourseRepository $courseRepository
     * @param Request          $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function search(
        CourseManager $courseManager,
        CourseRepository $courseRepository,
        Request $request
    ): Response {
        $form = $this->createForm(SearchType::class, null, ['advanced' => true]);
        $form->handleRequest($request);

        $courses = $courseManager->search($form);

        return $this->render('front/course/search.html.twig', [
            'form' => $form->createView(),
            'courses' => $courses,
        ]);
    }
}
