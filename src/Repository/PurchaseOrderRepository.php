<?php

namespace App\Repository;

use App\Entity\PurchaseOrder;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PurchaseOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseOrder[]    findAll()
 * @method PurchaseOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseOrderRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrder::class);
    }

    // /**
    //  * @return PurchaseOrder[] Returns an array of PurchaseOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PurchaseOrder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
