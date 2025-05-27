<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalPaymentGateway implements PaymentProcessorInterface
{
    public function __construct(private PaypalPaymentProcessor $paypalPaymentProcessor)
    {}

    public function pay(int $price): bool
    {
        try {
            $this->paypalPaymentProcessor->pay($price);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}