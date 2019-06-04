<?php

namespace App\Controller;

use App\Manager\TestimonialManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestimonialController extends AbstractController
{
    /**
     * @Route(path="/testimonials", name="testimonials", methods={"GET"})
     *
     * @param TestimonialManager $testimonialManager
     *
     * @return Response
     */
    public function list(TestimonialManager $testimonialManager): Response
    {
        return $this->render('front/testimonial/list.html.twig', [
            'testimonials' => $testimonialManager->collect(),
        ]);
    }
}
