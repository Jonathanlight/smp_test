<?php

namespace App\Controller\Api;

use App\Entity\Place;
use App\Manager\PlaceManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaceController extends AbstractController
{
    /**
     * @Route(path="/api/place/{id}/min", name="api_place_min_price", methods={"GET"})
     *
     * @param Place        $place
     * @param PlaceManager $placeManager
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function priceMin(Place $place, PlaceManager $placeManager): JsonResponse
    {
        return new JsonResponse($placeManager->getMinPriceByDepartment($place));
    }
}
