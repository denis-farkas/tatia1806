<?php
namespace App\Repository;

use App\Entity\ProductVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariant::class);
    }

    public function findAttributesByVariantId(int $variantId): array
    {
        $qb = $this->createQueryBuilder('pv')
            ->select('va.value AS attributeValue, a.name AS attributeName')
            ->join('pv.attributes', 'va') // Join with VariantAttribute
            ->join('va.attribute', 'a')  // Join with Attribute
            ->where('pv.id = :variantId')
            ->setParameter('variantId', $variantId);

        return $qb->getQuery()->getResult();
    }
}