<?php

namespace App\Controller\Middle;

use App\Entity\Payment;
use App\Form\Search\BaseSearchType;
use App\Manager\PaymentManager;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route(path="/cssr/payments", name="middle_payments", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\PaymentVoter::PAYMENT_VIEW'))")
     *
     * @param PaymentManager $paymentManager
     * @param Request        $request
     *
     * @return Response
     */
    public function list(
        PaymentManager $paymentManager,
        Request $request
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(BaseSearchType::class);

        $form->handleRequest($request);
        $filters = $form->getData();

        return $this->render('middle/payment/list.html.twig', [
            'payments' => $paymentManager->collect($user->getCenter(), $filters),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/order/{id}/recapitulatif", name="middle_payment_recapitulatif", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\PaymentVoter::PAYMENT_VIEW'))")
     *
     * @param Payment $payment
     *
     * @return Response
     */
    public function recapitulatif(Payment $payment): Response
    {
        $content = $this->renderView('middle/payment/recapitulatif.csv.twig', [
            'payment' => $payment,
        ]);

        $response = new Response($content);

        $response->headers->set('Content-Type', 'text/csv; charset=windows-1252');
        $response->headers->set('Content-Disposition', 'attachment; filename="recapitulatif.csv"');

        return $response;
    }

    /**
     * @Route("/cssr/order/{id}/invoice", name="middle_payment_invoice", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\PaymentVoter::PAYMENT_VIEW'))")
     *
     * @param Payment $payment
     * @param Pdf     $pdf
     *
     * @return PdfResponse
     */
    public function invoice(Payment $payment, Pdf $pdf): PdfResponse
    {
        $html = $this->renderView('pdf/facture.html.twig', [
            'payment' => $payment,
        ]);

        $options = [
            'header-html' => $this->renderView('pdf/_header-cssr.html.twig', [
                'center' => $payment->getCenter(),
            ]),
            'footer-html' => $this->renderView('pdf/_footer.html.twig', [
                'center' => $payment->getCenter(),
                'pager' => true,
            ]),
        ];

        return new PdfResponse(
            $pdf->getOutputFromHtml($html, $options),
            'facture.pdf'
        );
    }
}
