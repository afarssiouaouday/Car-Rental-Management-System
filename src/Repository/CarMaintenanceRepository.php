<?php

namespace App\Repository;

use App\Entity\CarMaintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarMaintenance>
 */
class CarMaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarMaintenance::class);
    }

    //    /**
    //     * @return CarMaintenance[] Returns an array of CarMaintenance objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CarMaintenance
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findInProgressMaintenances()
    {
        return $this->createQueryBuilder('cm')
            ->innerJoin('cm.car', 'c')
            ->addSelect('c')
            ->where('cm.status = :status')
            ->setParameter('status', 'En cours')
            ->getQuery()
            ->getResult();
    }
    
      
    
        
}
