<?php

namespace App\Controller\Api;

use App\Entity\Course;
use App\Manager\CourseManager;
use App\Manager\OrderManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseController extends AbstractController
{
    /**
     * @Route(path="/api/course/{id}/purchase", name="api_course_purchase", methods={"PUT"})
     *
     * @param Course       $course
     * @param OrderManager $orderManager
     *
     * @return JsonResponse
     */
    public function purchase(
        Course $course,
        OrderManager $orderManager
    ): JsonResponse {
        $orderManager->clear();
        $orderManager->generate($course);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/course/{id}/enable", name="api_course_enable", methods={"PUT"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_ENABLE'), course)")
     *
     * @param Course        $course
     * @param CourseManager $courseManager
     *
     * @return JsonResponse
     */
    public function enable(Course $course, CourseManager $courseManager): JsonResponse
    {
        $courseManager->enable($course);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/course/{id}/disable", name="api_course_disable", methods={"PUT"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_DISABLE'), course)")
     *
     * @param Course        $course
     * @param CourseManager $courseManager
     *
     * @return JsonResponse
     */
    public function disable(Course $course, CourseManager $courseManager): JsonResponse
    {
        $courseManager->disable($course);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/course/{id}/orders", name="api_course_orders", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CourseVoter::COURSE_VIEW'), course)")
     *
     * @param Course       $course
     * @param OrderManager $orderManager
     *
     * @return Response
     */
    public function orders(Course $course, OrderManager $orderManager): Response
    {
        return $this->render('middle/course/registered.html.twig', [
            'orders' => $orderManager->getRegisteredByCourse($course),
        ]);
    }

    /**
     * @Route(path="/api/courses", name="api_courses", methods={"POST"})
     *
     * @param CourseManager $courseManager
     * @param Request       $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function cheapest(CourseManager $courseManager, Request $request): Response
    {
        return $this->render('front/course/cheapest.html.twig', [
            'courses' => $courseManager->getCarouselCourses(
                $request->get('lat'),
                $request->get('lng'),
                (bool) $request->get('geolocated')
            ),
        ]);
    }
}
