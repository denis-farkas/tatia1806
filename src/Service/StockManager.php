<?php

namespace App\Service;

use App\Entity\ProductVariant;
use App\Entity\StockMovement;
use App\Enum\MovementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class StockManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function addStock(ProductVariant $variant, int $quantity, string $reason = 'Manual addition'): void
    {
        $variant->setQuantity($variant->getQuantity() + $quantity);
        $variant->setUpdatedAt(new \DateTimeImmutable());

        $this->createMovement($variant, MovementType::IN, $quantity, $reason);

        $this->entityManager->persist($variant);
        $this->entityManager->flush();
    }

    public function reserveStock(ProductVariant $variant, int $quantity): bool
    {
        if ($variant->getAvailableQuantity() < $quantity) {
            return false;
        }

        $variant->setReserved($variant->getReserved() + $quantity);
        $variant->setUpdatedAt(new \DateTimeImmutable());

        $this->createMovement($variant, MovementType::RESERVED, $quantity, 'Order reservation');

        $this->entityManager->persist($variant);
        $this->entityManager->flush();

        return true;
    }

    public function releaseStock(ProductVariant $variant, int $quantity): void
    {
        $variant->setReserved(max(0, $variant->getReserved() - $quantity));
        $variant->setUpdatedAt(new \DateTimeImmutable());

        $this->createMovement($variant, MovementType::OUT, $quantity, 'Order cancellation - Stock released');

        $this->entityManager->persist($variant);
        $this->entityManager->flush();
    }

    public function removeStock(ProductVariant $variant, int $quantity, string $reason = 'Manual removal'): bool
    {
        if ($variant->getQuantity() < $quantity) {
            return false;
        }

        $variant->setQuantity($variant->getQuantity() - $quantity);
        $variant->setUpdatedAt(new \DateTimeImmutable());

        $this->createMovement($variant, MovementType::OUT, $quantity, $reason);

        $this->entityManager->persist($variant);
        $this->entityManager->flush();

        return true;
    }

    public function checkStockAvailability(ProductVariant $variant, int $requestedQuantity): bool
    {
        return $variant->getAvailableQuantity() >= $requestedQuantity;
    }

    private function createMovement(ProductVariant $variant, MovementType $type, int $quantity, string $reason): void
    {
        $movement = new StockMovement();
        $movement->setVariant($variant);
        $movement->setType($type);
        $movement->setQuantity($quantity);
        $movement->setReason($reason);
        $movement->setCreatedAt(new \DateTime());
        $movement->setCreatedBy($this->security->getUser());

        $this->entityManager->persist($movement);
    }

    public function confirmStock(ProductVariant $variant, int $quantity): void
    {
        // Deduct the reserved stock
        $variant->setReserved(max(0, $variant->getReserved() - $quantity));

        // Deduct the actual stock
        $variant->setQuantity(max(0, $variant->getQuantity() - $quantity));

        $variant->setUpdatedAt(new \DateTimeImmutable());

        $this->createMovement($variant, MovementType::OUT, $quantity, 'Order confirmed - Stock deducted');

        $this->entityManager->persist($variant);
        $this->entityManager->flush();
    }
}