<?php

namespace App\Message;

use App\Entity\Enum\PaymentProcessor;
use Symfony\Component\Validator\Constraints as Assert;

final class PurchaseMessage
{
    #[Assert\NotBlank(message:"Неверная цена")]
    private ?float $productPrice;
    #[Assert\NotBlank(message:"Неверный тип платежа")]
    private ?PaymentProcessor $paymentProcessor;

    public function __construct(?array $data, float $productPrice)
    {
        $this->productPrice = $productPrice;

        if ($data && isset($data['paymentProcessor'])) {
            $this->paymentProcessor =  PaymentProcessor::tryFrom($data['paymentProcessor']);
        }
    }

    public function getProductPrice(): float
    {
        return $this->productPrice;
    }

    public function getPaymentProcessor(): PaymentProcessor
    {
        return $this->paymentProcessor;
    }
}
