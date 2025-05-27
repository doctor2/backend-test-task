<?php

namespace App\Message\Handler;

use App\Entity\Enum\PaymentProcessor;
use App\Message\PurchaseMessage;
use App\Service\PaypalPaymentGateway;
use App\Service\StripePaymentGateway;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PurchaseMessageHandler
{
    public function __construct(private PaypalPaymentGateway $paypalPaymentGateway, private StripePaymentGateway $stripePaymentGateway)
    {
    }

    public function __invoke(PurchaseMessage $message): void
    {
        $isPayed = false;

        if ($message->getPaymentProcessor() === PaymentProcessor::PAYPAL) {
            $isPayed = $this->paypalPaymentGateway->pay($message->getProductPrice());
        } elseif ($message->getPaymentProcessor() === PaymentProcessor::STRIPE) {
            $isPayed = $this->stripePaymentGateway->pay($message->getProductPrice());
        }

        if (!$isPayed){
            throw new \Exception('Ошибка оплаты: ' . $message->getPaymentProcessor()->value);
        }
    }
}
