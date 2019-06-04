<?php

namespace App\Controller\Middle;

use App\Entity\Center;
use App\Form\Search\ReportingSearchType;
use App\Services\ReportingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ReportingController extends AbstractController
{
    /**
     * @Route(path="/cssr/reporting", name="middle_reporting_list", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\ReportingVoter::REPORTING_VIEW'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function list(Request $request): Response
    {
        $form = $this->createForm(ReportingSearchType::class);
        $form->handleRequest($request);

        return $this->render('middle/reporting/list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/cssr/reporting/trainee", name="middle_reporting_order", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\ReportingVoter::REPORTING_ORDER'))")
     *
     * @param ReportingService $reportingService
     * @param Request          $request
     *
     * @return StreamedResponse
     */
    public function order(ReportingService $reportingService, Request $request): Response
    {
        $form = $this->createForm(ReportingSearchType::class);
        $form->handleRequest($request);

        return $reportingService->exportOrder($form);
    }

    /**
     * @Route(path="/cssr/reporting/center/{id}", name="middle_reporting_center", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\ReportingVoter::REPORTING_CENTER'))")
     *
     * @param ReportingService $reportingService
     * @param Center           $center
     * @param Request          $request
     *
     * @return StreamedResponse
     */
    public function center(ReportingService $reportingService, Center $center, Request $request): Response
    {
        $form = $this->createForm(ReportingSearchType::class);
        $form->handleRequest($request);

        return $reportingService->exportCssr($center, $form);
    }
}
