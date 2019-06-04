<?php

namespace App\Controller;

use App\Entity\Trainee;
use App\Entity\Transaction;
use App\Form\OrderType;
use App\Payment\Ogone\PaymentType;
use App\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class OrderController extends AbstractController
{
    /**
     * @Route(path="/order/user", name="order_user", methods={"GET","POST"})
     *
     * @param Request      $request
     * @param OrderManager $manager
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function user(
        Request $request,
        OrderManager $manager
    ): Response {
        $order = $manager->load();
        $trainee = $order->getTrainee();

        if (!$trainee instanceof Trainee) {
            $trainee = new Trainee();
            $order->setTrainee($trainee);
            $trainee->setOrder($order);
        }

        $canSendLetter = $order->getCourse()->getStartOn() >= new \DateTime(date('Y-m-d', strtotime('+2 day')));

        $form = $this->createForm(OrderType::class, $trainee, [
            'step' => 1,
            'canSendLetter' => $canSendLetter,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && ($form->isValid())) {
            if ($form->has('sendLetter') && $form->get('sendLetter')->getData()) {
                $manager->addSendLetter($order);
            } else {
                $manager->removeSendLetter($order);
            }

            $manager->update($order, 1);

            return $this->redirectToRoute('order_course');
        }

        return $this->render('front/order/user.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'canSendLetter' => $canSendLetter,
        ]);
    }

    /**
     * @Route(path="/order/course", name="order_course", methods={"GET","POST"})
     *
     * @param Request      $request
     * @param OrderManager $manager
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function course(
        Request $request,
        OrderManager $manager
    ): Response {
        $order = $manager->load();
        $trainee = $order->getTrainee();

        $form = $this->createForm(OrderType::class, $trainee, ['step' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && ($form->isValid())) {
            $manager->update($order, 2);

            return $this->redirectToRoute('order_payment');
        }

        return $this->render('front/order/course.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * @Route(path="/order/payment", name="order_payment", methods={"GET","POST"}))
     *
     * @param Request             $request
     * @param OrderManager        $manager
     * @param TranslatorInterface $translator
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function payment(
        Request $request,
        OrderManager $manager,
        TranslatorInterface $translator
    ): Response {
        $order = $manager->load();
        $trainee = $order->getTrainee();

        $error = null;

        $formCodePromo = $this->createForm(OrderType::class, $trainee, ['step' => 3]);
        $formCodePromo->handleRequest($request);

        if ($formCodePromo->isSubmitted() && $formCodePromo->isValid()) {
            if (!$manager->checkCoupon($order, $formCodePromo->get('codePromo')->getData())) {
                $error = $translator->trans('form.step3.code_not_valide');
            }

            $manager->update($order, 3);
        }

        $payment = $manager->createPayment($order);
        $form = $this->createForm(PaymentType::class, $payment);

        return $this->render('front/order/payment.html.twig', [
            'order' => $order,
            'error' => $error,
            'form' => $form->createView(),
            'formCodePromo' => $formCodePromo->createView(),
        ]);
    }

    /**
     * @Route(path="/order/summary", name="order_summary", methods={"GET"})
     *
     * @param Request      $request
     * @param OrderManager $manager
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Response
     */
    public function summary(
        Request $request,
        OrderManager $manager
    ): Response {
        $order = $manager->load();

        if (null === $order) {
            return $this->redirectToRoute('homepage');
        }

        if (Transaction::STATUS_ACCEPTED_PAYMENT == $request->query->get('STATUS')) {
            $manager->update($order, 4);
        }

        $manager->addTransaction($order, $request);

        return $this->render('front/order/summary.html.twig', [
            'order' => $order,
        ]);
    }
}
