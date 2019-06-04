<?php

namespace App\Controller\Api;

use App\Entity\Center;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route(path="/api/user/center", name="api_center_empty", methods={"PUT"})
     * @Route(path="/api/user/center/{id}", name="api_center_update", methods={"PUT"})
     *
     * @param Center      $center
     * @param UserManager $userManager
     *
     * @return JsonResponse
     */
    public function setCenter(
        ?Center $center,
        UserManager $userManager
    ): JsonResponse {
        $userManager->setCenter($this->getUser(), $center);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
