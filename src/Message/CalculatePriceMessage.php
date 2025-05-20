<?php

namespace App\Message;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\Tax;
use App\Validator\Constraints\EntityExists\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

final class CalculatePriceMessage
{
    #[Assert\NotBlank(message:"Поле product отсутствует")]
    #[EntityExists(message:"Продукт не найден.", entityClass:Product::class)]
    public ?int $product;
    #[Assert\NotBlank(message:"Поле taxNumber отсутствует")]
    #[Assert\Regex(pattern:"/^[A-Z]{2,4}\d{9,12}$/", message:"Налоговый номер не верен")]
    #[EntityExists(message:"Налог не найден", entityClass:Tax::class, field:'taxNumber')]
    public ?string $taxNumber;
    #[EntityExists(message:"Купон не найден", entityClass:Coupon::class, field:'code')]
    public ?string $couponCode = null;

    public function getProductId(): int
    {
        return $this->product;
    }

    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }
}
