<?php

namespace App\Controller\Api;

use App\Entity\Center;
use App\Entity\Course;
use App\Manager\CenterManager;
use App\Manager\CourseManager;
use App\Repository\CourseRepository;
use App\Services\SerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CenterController extends AbstractController
{
    /**
     * @Route(path="/api/centers", name="api_center_lists", methods={"GET"})
     *
     * @param Request           $request
     * @param CenterManager     $centerManager
     * @param CourseRepository  $courseRepository
     * @param SerializerService $serializerService
     *
     * @return JsonResponse
     */
    public function list(
        Request $request,
        CenterManager $centerManager,
        CourseRepository $courseRepository,
        SerializerService $serializerService
    ): JsonResponse {
        if (!$request->query->has('course')) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $course = $courseRepository->find(intval($request->query->get('course')));

        if (!$course instanceof Course) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($serializerService->serialize($centerManager->collectEnabled($course)));
    }

    /**
     * @Route(path="/api/center/{id}/courses", name="api_center_courses", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\CenterVoter::CENTER_VIEW'))")
     *
     * @param Request           $request
     * @param Center            $center
     * @param CourseManager     $courseManager
     * @param CourseRepository  $courseRepository
     * @param SerializerService $serializerService
     *
     * @return JsonResponse
     */
    public function courses(
        Request $request,
        Center $center,
        CourseManager $courseManager,
        CourseRepository $courseRepository,
        SerializerService $serializerService
    ): JsonResponse {
        if (!$request->query->has('course')) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $course = $courseRepository->find(intval($request->query->get('course')));

        if (!$course instanceof Course) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $courses = $courseManager->collectByCenter(
            $center,
            $course,
            null,
            boolval($request->query->get('hasSamePrice'))
        );

        return new JsonResponse($serializerService->serialize($courses));
    }
}
