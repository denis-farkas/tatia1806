<?php

namespace App\Repository;

use App\Entity\UserCours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCours>
 */
class UserCoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCours::class);
    }

    /**
     * Get courses for a specific user
     *
     * @param int $userId
     * @return UserCours[]
     */
    public function findByUserWithCoursDetails(int $userId): array
    {
        return $this->createQueryBuilder('uc')
            ->join('uc.cours', 'c')
            ->addSelect('c') // Include course details
            ->where('uc.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.name', 'ASC') // Optional: Order by course name
            ->getQuery()
            ->getResult();
    }
}
