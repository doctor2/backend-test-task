<?php

namespace App\Entity;

use App\Entity\Enum\DiscountType;
use App\Repository\CouponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ORM\Table(name: 'coupons')]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $code;

    #[ORM\Column(enumType: DiscountType::class, length: 10)]
    private DiscountType $discountType;

    #[ORM\Column(type: 'integer')]
    private ?int $discountValue;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $validFrom = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $validTo = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $usageLimit = null;

    #[ORM\Column(type: 'integer')]
    private int $usedCount = 0;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'coupon')]
    private Collection $products;

    public function __construct()
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getDiscountType(): DiscountType
    {
        return $this->discountType;
    }

    public function setDiscountType(DiscountType $discountType): self
    {
        $this->discountType = $discountType;
        return $this;
    }

    public function getDiscountValue(): int
    {
        return $this->discountValue;
    }

    public function setDiscountValue(int $discountValue): self
    {
        $this->discountValue = $discountValue;
        return $this;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(?\DateTimeInterface $validFrom): self
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;
        return $this;
    }

    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    public function setUsageLimit(?int $usageLimit): self
    {
        $this->usageLimit = $usageLimit;
        return $this;
    }

    public function getUsedCount(): int
    {
        return $this->usedCount;
    }

    public function setUsedCount(int $usedCount): self
    {
        $this->usedCount = $usedCount;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Метод для проверки, действителен ли купон
    public function isValid(): bool
    {
        $now = new \DateTime();

        if ($this->validFrom && $this->validFrom > $now) {
            return false;
        }
        if ($this->validTo && $this->validTo < $now) {
            return false;
        }
        if ($this->usageLimit !== null && $this->usedCount >= $this->usageLimit) {
            return false;
        }
        return true;
    }

    // Метод для увеличения счетчика использований
    public function incrementUsage(): void
    {
        $this->usedCount++;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCoupon($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCoupon() === $this) {
                $product->setCoupon(null);
            }
        }

        return $this;
    }
}
