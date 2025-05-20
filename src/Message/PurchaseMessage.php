<?php

namespace App\Message;

use App\Entity\Enum\PaymentProcessor;
use Symfony\Component\Validator\Constraints as Assert;

final class PurchaseMessage
{
    #[Assert\NotBlank(message:"Неверная цена")]
    public ?float $productPrice;
    #[Assert\NotBlank(message:"Неверный тип платежа")]
    public ?PaymentProcessor $paymentProcessor;

    public function getProductPrice(): float
    {
        return $this->productPrice;
    }

    public function getPaymentProcessor(): PaymentProcessor
    {
        return $this->paymentProcessor;
    }
}
