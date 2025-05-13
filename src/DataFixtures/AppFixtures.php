<?php

namespace App\DataFixtures;

use App\Entity\Enum\DiscountType;
use App\Factory\CouponFactory;
use App\Factory\ProductFactory;
use App\Factory\TaxFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $coupon1 = CouponFactory::new(['discountValue' => '15', 'discountType' => DiscountType::FIXED, 'code' => 'D15'])->create();
        $coupon2 = CouponFactory::new(['discountValue' => '100', 'discountType' => DiscountType::PERCENT, 'code' => 'D100'])->create();
        CouponFactory::createMany(5);

        ProductFactory::new(['name' => 'Iphone', 'price' => 100, 'coupon' => $coupon2])->create();
        ProductFactory::new(['name' => 'Наушники', 'price' => 20, 'coupon' => $coupon1])->create();
        ProductFactory::new(['name' => 'Чехол', 'price' => 10, 'coupon' => $coupon1])->create();
        ProductFactory::createMany(5);

        TaxFactory::new(['taxNumber' =>'IT12345678900', 'rate' => 22])->create();
        TaxFactory::new(['taxNumber' =>'FRYY123456789', 'rate' => 20])->create();
        TaxFactory::new(['taxNumber' =>'DE123456789', 'rate' => 17])->create();
    }
}
