<?php

namespace App\Message\Handler;

use App\Entity\Coupon;
use App\Entity\Enum\DiscountType;
use App\Entity\Product;
use App\Entity\Tax;
use App\Message\CalculatePriceMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CalculatePriceMessageHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function __invoke(CalculatePriceMessage $message): float
    {
        $productPrice = $this->getProductPrice($message->getProductId());
        $discount = $this->applyDiscount($message->getCouponCode(), $message->getProductId(), $productPrice);

        $priceWithDiscount = $productPrice - $discount;

        $tax = $this->calculateTax($message->getTaxNumber(), $priceWithDiscount);

        return $priceWithDiscount + $tax;
    }

    private function getProductPrice(int $productId): int
    {
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->find($productId);

        return $product->getPrice();
    }

    private function applyDiscount(?string $couponCode, int $productId, int $price): int
    {
        if (!$couponCode) {
            return 0;
        }

        $couponRepository = $this->entityManager->getRepository(Coupon::class);
        $coupon = $couponRepository->findByCodeAndProductId($couponCode,  $productId);

        if ($coupon?->getDiscountType() === DiscountType::FIXED){
            return $coupon->getDiscountValue();
        } elseif ($coupon?->getDiscountType() === DiscountType::PERCENT) {
            return (int) ($price * $coupon->getDiscountValue() / 100);
        }

        return 0;
    }

    private function calculateTax(string $taxNumber, int $price): int
    {
        $taxRepository = $this->entityManager->getRepository(Tax::class);
        $tax = $taxRepository->findOneBy(['taxNumber' => $taxNumber]);

        return (int) ($price * $tax->getRate() / 100);
    }
}
