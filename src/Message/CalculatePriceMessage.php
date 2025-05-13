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
    private ?int $productId;
    #[Assert\NotBlank(message:"Поле taxNumber отсутствует")]
    #[Assert\Regex(pattern:"/^[A-Z]{2,4}\d{9,12}$/", message:"Налоговый номер не верен")]
    #[EntityExists(message:"Налог не найден", entityClass:Tax::class, field:'taxNumber')]
    private ?string $taxNumber;
    #[EntityExists(message:"Купон не найден", entityClass:Coupon::class, field:'code')]
    private ?string $couponCode = null;

    public function __construct(?array $data)
    {
        if ($data && isset($data['product'])) {
            $this->productId = $data['product'];
        }

        if ($data && isset($data['taxNumber'])) {
            $this->taxNumber = $data['taxNumber'];
        }

        if ($data && isset($data['couponCode'])) {
            $this->couponCode = $data['couponCode'];
        }
    }

    public function getProductId(): int
    {
        return $this->productId;
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
