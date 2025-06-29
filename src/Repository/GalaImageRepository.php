<?php

namespace App\Repository;

use App\Entity\GalaImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GalaImagePhp>
 */
class GalaImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GalaImage::class);
    }

    //    /**
    //     * @return GalaImagePhp[] Returns an array of GalaImagePhp objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GalaImagePhp
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findFirstImagesByGala(string $galaname): array
    {
        return $this->createQueryBuilder('gi')
            ->select('gi, c.name AS coursName, c.age AS coursAge, c.salle AS coursSalle')
            ->join('App\Entity\Cours', 'c', 'WITH', 'gi.cours = c.id')
            ->where('gi.galaname = :galaname')
            ->andWhere('gi.id = (
                SELECT MIN(gi2.id)
                FROM App\Entity\GalaImage gi2
                WHERE gi2.cours = gi.cours AND gi2.galaname = :galaname
            )')
            ->setParameter('galaname', $galaname)
            ->getQuery()
            ->getResult();
    }
}
