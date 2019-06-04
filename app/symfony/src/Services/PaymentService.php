<?php

namespace App\Services;

class PaymentService
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function groupByDate(array $data): array
    {
        $responses = [];

        foreach ($data as $payment) {
            if (!isset($responses[$payment->getGeneratedAt()->format('Y')])) {
                $responses[$payment->getGeneratedAt()->format('Y')] = [];
                if (!isset($responses[$payment->getGeneratedAt()->format('Y')][\strftime('%B', $payment->getGeneratedAt()->getTimestamp())])) {
                    $responses[$payment->getGeneratedAt()->format('Y')][\strftime('%B', $payment->getGeneratedAt()->getTimestamp())] = [];
                }
            }

            $responses[$payment->getGeneratedAt()->format('Y')][\strftime('%B', $payment->getGeneratedAt()->getTimestamp())][] = $payment;
        }

        return $responses;
    }
}
