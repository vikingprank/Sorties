<?php

namespace App\Repository;

use App\Entity\SortieLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SortieLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortieLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortieLike[]    findAll()
 * @method SortieLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortieLike::class);
    }

    // /**
    //  * @return SortieLike[] Returns an array of SortieLike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SortieLike
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
