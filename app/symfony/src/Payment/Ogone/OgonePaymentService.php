<?php

namespace App\Payment\Ogone;

use App\Entity\Order;
use App\Entity\Parameter;
use App\Entity\Transaction;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class OgonePaymentService
{
    /**
     * @var string
     */
    protected $ogonePspId;

    /**
     * @var string
     */
    protected $ogoneCurrency;

    /**
     * @var string
     */
    protected $ogoneLanguage;

    /**
     * @var string
     */
    protected $keySecret;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var OrderManager
     */
    protected $orderManager;

    public function __construct(
        string $ogonePspId,
        string $ogoneCurrency,
        string $ogoneLanguage,
        EntityManagerInterface $em,
        string $keySecret
    ) {
        $this->ogonePspId = $ogonePspId;
        $this->ogoneCurrency = $ogoneCurrency;
        $this->ogoneLanguage = $ogoneLanguage;
        $this->em = $em;
        $this->keySecret = $keySecret;
    }

    /**
     * @param Order $order
     *
     * @return PaymentModel
     */
    public function createPayment(Order $order): PaymentModel
    {
        $payment = new PaymentModel();

        $payment->setPspid($this->ogonePspId);
        $payment->setOrderId(join('x', [$order->getId(), time()]));
        $payment->setAmount(($order->getAmount() * 100));
        $payment->setCurrency($this->ogoneCurrency);
        $payment->setLanguage($this->ogoneLanguage);

        $payment->setEmail(Parameter::EMAIL_DEFAULT);

        if ($order->getTrainee()->getEmail()) {
            $payment->setEmail($order->getTrainee()->getEmail());
        }

        $payment->setShasign($this->cryptPayment($payment));

        return $payment;
    }

    /**
     * @param PaymentModel $payment
     *
     * @return string
     */
    public function cryptPayment(PaymentModel $payment)
    {
        $cryptData = '';

        $keySecret = $this->keySecret;

        $tabPayment = [
            'AMOUNT' => $payment->getAmount(),
            'CURRENCY' => $payment->getCurrency(),
            'EMAIL' => $payment->getEmail(),
            'HOMEURL' => 'NONE',
            'LANGUAGE' => $payment->getLanguage(),
            'ORDERID' => $payment->getOrderid(),
            'PSPID' => $payment->getPspid(),
        ];

        foreach ($tabPayment as $key => $value) {
            $cryptData .= $key.'='.$value.$keySecret;
        }

        return hash('sha256', $cryptData);
    }

    /**
     * @param Request $request
     * @param Order   $order
     */
    public function createTransaction(Request $request, Order $order)
    {
        $query = $request->query;

        if (!$query->get('PAYID')) {
            return;
        }

        $transaction = new Transaction();

        $transaction->setAmount($query->get('amount'));
        $transaction->setOrder($order);
        $transaction->setOgoneId($query->get('PAYID'));
        $transaction->setCardType($query->get('BRAND'));
        $transaction->setNamePaymentCard($query->get('CN'));
        $transaction->setCodeError($query->get('NCERROR'));
        $transaction->setMeansPayment($query->get('PM'));
        $transaction->setStatus($query->get('STATUS'));
        $transaction->setDate(new \DateTime());

        $order->addTransaction($transaction);
    }
}
