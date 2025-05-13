<?php

namespace App\Repository;

use App\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupon>
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function findByCodeAndProductId(string $couponCode, int $productId): ?Coupon
    {
        return $this->createQueryBuilder('c')
            ->join('c.products', 'p')
            ->andWhere('c.code = :couponCode')
            ->andWhere('p.id = :productId')
            ->setParameter('couponCode', $couponCode)
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
