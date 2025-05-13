<?php

namespace App\Message\Handler;

use App\Entity\Enum\PaymentProcessor;
use App\Message\PurchaseMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

#[AsMessageHandler]
final class PurchaseMessageHandler
{
    public function __construct(private PaypalPaymentProcessor $paypalPaymentProcessor, private StripePaymentProcessor $stripePaymentProcessor)
    {
    }

    public function __invoke(PurchaseMessage $message): void
    {
        $isPayed = true;

        if ($message->getPaymentProcessor() === PaymentProcessor::PAYPAL) {
            $this->paypalPaymentProcessor->pay($message->getProductPrice());
        } elseif ($message->getPaymentProcessor() === PaymentProcessor::STRIPE) {
            $isPayed = $this->stripePaymentProcessor->processPayment($message->getProductPrice());
        }

        if (!$isPayed){
            throw new \Exception('Ошибка оплаты');
        }
    }
}
