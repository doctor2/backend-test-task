<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripePaymentGateway implements PaymentProcessorInterface
{
    public function __construct(private StripePaymentProcessor $stripePaymentProcessor)
    {}

    public function pay(int $price): bool
    {
        return $this->stripePaymentProcessor->processPayment($price);
    }
}