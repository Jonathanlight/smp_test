<?php

namespace App\Controller\Api;

use App\Entity\Course;
use App\Entity\Order;
use App\Entity\User;
use App\Form\RefundOrderType;
use App\Manager\OrderManager;
use App\Repository\CourseRepository;
use App\Repository\OrderRepository;
use App\Services\SerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order/{id}", name="api_order_view", methods={"GET"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_VIEW'), order)")
     *
     * @param Order             $order
     * @param SerializerService $serializerService
     *
     * @return JsonResponse
     */
    public function view(
        Order $order,
        SerializerService $serializerService
    ): JsonResponse {
        return new JsonResponse($serializerService->serialize($order));
    }

    /**
     * @Route(path="/api/order/{id}/confirm", name="api_order_confirm", methods={"PUT"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_CONFIRM'), order)")
     *
     * @param Order        $order
     * @param OrderManager $orderManager
     *
     * @return JsonResponse
     */
    public function confirm(
        Order $order,
        OrderManager $orderManager
    ): JsonResponse {
        $orderManager->confirm($order);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/order/{id}/cancel", name="api_order_cancel", methods={"PUT"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_CANCEL'), order)")
     *
     * @param Request      $request
     * @param Order        $order
     * @param OrderManager $orderManager
     *
     * @return JsonResponse
     */
    public function cancel(
        Request $request,
        Order $order,
        OrderManager $orderManager
    ): JsonResponse {
        if (!$request->request->has('reason')) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $orderManager->cancel($order, $request->request->get('reason'));

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/order/{id}/wait", name="api_order_wait", methods={"PUT"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_WAIT'), order)")
     *
     * @param Order        $order
     * @param OrderManager $orderManager
     *
     * @return JsonResponse
     */
    public function wait(
        Order $order,
        OrderManager $orderManager
    ): JsonResponse {
        $orderManager->wait($order);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/order/{id}/refund", name="api_order_refund", methods={"GET","POST"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_REFUND'), order)")
     *
     * @param Request      $request
     * @param Order        $order
     * @param OrderManager $orderManager
     *
     * @return Response
     */
    public function refund(
        Request $request,
        Order $order,
        OrderManager $orderManager
    ): Response {
        $form = $this->createForm(RefundOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $orderManager->refund($order);

                return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
            }

            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors['global'] = $error->getMessage();
                foreach ($form as $key => $child) {
                    if ($child->isValid()) {
                        continue;
                    }
                    foreach ($child->getErrors() as $error) {
                        $errors['fields'][$child->getName()][] = $error->getMessage();
                    }
                }
            }

            return new JsonResponse($errors, JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->render('middle/order/_refund.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/api/order/{id}/transfer", name="api_order_transfer", methods={"PUT"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\OrderVoter::ORDER_TRANSFER'), order)")
     *
     * @param Request          $request
     * @param Order            $order
     * @param CourseRepository $courseRepository
     * @param OrderManager     $orderManager
     *
     * @return JsonResponse
     */
    public function transfer(
        Request $request,
        Order $order,
        CourseRepository $courseRepository,
        OrderManager $orderManager
    ): JsonResponse {
        if (!$request->request->has('course')) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $course = $courseRepository->find(intval($request->request->get('course')));

        if (!$course instanceof Course) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $orderManager->transfer($order, $course);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/orders/cancel", name="api_orders_cancel", methods={"PUT"})
     *
     * @param Request         $request
     * @param OrderManager    $orderManager
     * @param OrderRepository $orderRepository
     *
     * @return JsonResponse
     */
    public function multipleCancel(
        Request $request,
        OrderManager $orderManager,
        OrderRepository $orderRepository
    ): JsonResponse {
        $orders = $request->request->get('ids');
        $user = $this->getUser();

        if (empty($orders)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($orders as $id) {
            $order = $orderRepository->find($id);

            if (!$order instanceof Order
                || Order::STATUS_CANCELLED === $order->getStatus()
                || User::ROLE_CSSR === $user->getRole() && $order->getCourse()->getCenter() !== $user->getCenter()) {
                continue;
            }

            $orderManager->cancel($order, $request->request->get('reason'));
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/orders/wait", name="api_orders_wait", methods={"PUT"})
     *
     * @param Request         $request
     * @param OrderManager    $orderManager
     * @param OrderRepository $orderRepository
     *
     * @return JsonResponse
     */
    public function multipleWait(
        Request $request,
        OrderManager $orderManager,
        OrderRepository $orderRepository
    ): JsonResponse {
        $orders = $request->request->get('ids');
        $user = $this->getUser();

        if (empty($orders)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($orders as $id) {
            $order = $orderRepository->find($id);

            if (!$order instanceof Order
                || Order::STATUS_WAITING === $order->getStatus()
                || User::ROLE_CSSR === $user->getRole() && $order->getCourse()->getCenter() !== $user->getCenter()) {
                continue;
            }
            $orderManager->wait($order);
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/api/orders/confirm", name="api_orders_confirm", methods={"PUT"})
     *
     * @param Request         $request
     * @param OrderManager    $orderManager
     * @param OrderRepository $orderRepository
     *
     * @return JsonResponse
     */
    public function multipleConfirm(
        Request $request,
        OrderManager $orderManager,
        OrderRepository $orderRepository
    ): JsonResponse {
        $orders = $request->request->get('ids');
        $user = $this->getUser();

        if (empty($orders)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($orders as $id) {
            $order = $orderRepository->find($id);

            if (!$order instanceof Order
                || Order::STATUS_CONFIRMED === $order->getStatus()
                || User::ROLE_CSSR === $user->getRole() && $order->getCourse()->getCenter() !== $user->getCenter()) {
                continue;
            }

            $orderManager->confirm($order);
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
