<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class ProductVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'variants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\OneToMany(mappedBy: 'variant', targetEntity: VariantAttribute::class, cascade: ['persist', 'remove'])]
    private Collection $attributes;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $reserved = 0; // Default value set to 0

    #[ORM\Column]
    private ?int $minQuantity = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        // Ensure reserved is initialized
        if ($this->reserved === null) {
            $this->reserved = 0;
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->reserved = 0; // Initialize reserved to 0
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(VariantAttribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setVariant($this);
        }

        return $this;
    }

    public function removeAttribute(VariantAttribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            if ($attribute->getVariant() === $this) {
                $attribute->setVariant(null);
            }
        }

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getReserved(): ?int
    {
        return $this->reserved;
    }

    public function setReserved(int $reserved): self
    {
        $this->reserved = $reserved;
        return $this;
    }

    public function getMinQuantity(): ?int
    {
        return $this->minQuantity;
    }

    public function setMinQuantity(int $minQuantity): self
    {
        $this->minQuantity = $minQuantity;
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getAvailableQuantity(): int
    {
        return max(0, $this->quantity - $this->reserved);
    }

    public function isInStock(): bool
    {
        return $this->getAvailableQuantity() > 0;
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minQuantity;
    }

    public function __toString(): string
    {
        $attributes = $this->attributes->map(fn($attr) => $attr->getAttribute()->getName() . ': ' . $attr->getValue())->toArray();
        return implode(', ', $attributes);
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAttributesAsArray(): array
    {
        return $this->attributes->map(function (VariantAttribute $attribute) {
            return [
                'name' => $attribute->getAttribute()->getName(), // Get the attribute name
                'value' => $attribute->getValue(), // Get the attribute value
            ];
        })->toArray();
    }
}