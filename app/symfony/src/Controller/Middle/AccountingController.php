<?php

namespace App\Controller\Middle;

use App\Entity\Center;
use App\Services\ReportingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class AccountingController extends AbstractController
{
    /**
     * @Route(path="/cssr/accounting", name="middle_accounting_list", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\AccountingVoter::ACCOUNTING_VIEW'))")
     *
     * @return Response
     */
    public function list(): Response
    {
        return $this->render('middle/accounting/list.html.twig');
    }

    /**
     * @Route(path="/cssr/accounting/fee", name="middle_accounting_fee", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\AccountingVoter::ACCOUNTING_FEE'))")
     *
     * @param ReportingService $reportingService
     * @param Center|null      $center
     *
     * @return StreamedResponse
     */
    public function fee(ReportingService $reportingService): Response
    {
        return $reportingService->exportApplicationFee();
    }

    /**
     * @Route(path="/cssr/accounting/refund", name="middle_accounting_refund", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\AccountingVoter::ACCOUNTING_REFUND'))")
     *
     * @param ReportingService $reportingService
     * @param Center           $center
     *
     * @return StreamedResponse
     */
    public function refund(ReportingService $reportingService): Response
    {
        return $reportingService->exportOrderRefund();
    }

    /**
     * @Route(path="/cssr/accounting/banking", name="middle_accounting_banking", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\AccountingVoter::ACCOUNTING_BANKING'))")
     *
     * @param ReportingService $reportingService
     *
     * @return Response
     */
    public function banking(ReportingService $reportingService): Response
    {
        $export = $reportingService->getExportBankZip();

        $response = new Response(file_get_contents($export));
        $response->headers->set('Content-Type', 'text/csv; charset=windows-1252');
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$export.'"');
        $response->headers->set('Content-length', filesize($export));

        unlink($export);

        return $response;
    }
}
