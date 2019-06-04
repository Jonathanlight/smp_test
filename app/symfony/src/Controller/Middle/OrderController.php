<?php

namespace App\Controller\Middle;

use App\Entity\Center;
use App\Entity\Order;
use App\Form\Search\OrderSearchType;
use App\Manager\OrderManager;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route(path="/cssr/orders", name="middle_orders", methods={"GET", "POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_LIST'))")
     *
     * @param OrderManager $orderManager
     * @param Request      $request
     *
     * @return Response
     */
    public function list(
        OrderManager $orderManager,
        Request $request
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(OrderSearchType::class, null, [
            'user' => $user,
        ]);

        $form->handleRequest($request);
        $filters = $form->getData();

        if ($user->getCenter() instanceof Center) {
            $filters['role'] = $user->getRole();
            $filters['code'] = $user->getCenter()->getCode();
        }

        return $this->render('middle/order/list.html.twig', [
            'orders' => $orderManager->collect($filters),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cssr/order/{id}", name="middle_order", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_VIEW'), order)")
     *
     * @param Order $order
     *
     * @return Response
     */
    public function view(Order $order): Response
    {
        return $this->render('middle/order/index.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/cssr/order/{id}/convocation", name="middle_order_convocation", methods={"GET"})
     *
     * @param Order        $order
     * @param OrderManager $manager
     *
     * @return PdfResponse
     */
    public function convocation(
        Order $order,
        OrderManager $manager
    ): PdfResponse {
        return new PdfResponse(
            $manager->generateConvocation($order),
            'convocation.pdf'
        );
    }
}
