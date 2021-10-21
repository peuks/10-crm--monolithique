<?php

namespace App\Repository;

use App\Entity\TinyKangaroo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TinyKangaroo|null find($id, $lockMode = null, $lockVersion = null)
 * @method TinyKangaroo|null findOneBy(array $criteria, array $orderBy = null)
 * @method TinyKangaroo[]    findAll()
 * @method TinyKangaroo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TinyKangarooRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TinyKangaroo::class);
    }

    // /**
    //  * @return TinyKangaroo[] Returns an array of TinyKangaroo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TinyKangaroo
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
