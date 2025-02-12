<?php

namespace App\Repository;

use App\Entity\Car;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

//    /**
//     * @return Car[] Returns an array of Car objects
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

//    public function findOneBySomeField($value): ?Car
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
      public function carAsvailable(User $user){
        return $this->createQueryBuilder('c')
          ->where('c.status = :available')
          ->andwhere('c.User = :user')
          ->setParameter('available', 'Disponible')
          ->setParameter('user', $user)
          ->getQuery()
          ->getResult();
      }

      public function findAvailableCars(DateTimeInterface $startDate, DateTimeInterface $endDate, User $user)
      {
          $qb = $this->createQueryBuilder('c')
              ->where('c.User = :user')
              ->setParameter('user',$user);
  
          $qb->andWhere('NOT EXISTS (
                  SELECT b 
                  FROM App\Entity\Booking b 
                  WHERE b.car = c 
                  AND b.status != :cancelledStatus
                  AND NOT (
                      b.end_date < :startDate 
                      OR b.start_date > :endDate
                  )
              )')
              ->setParameter('startDate', $startDate)
              ->setParameter('endDate', $endDate)
              ->setParameter('cancelledStatus', 'annulee');
  
          
  
          return $qb->getQuery()->getResult();
      }

      public function carMaintenance(User $user) {
        return $this->createQueryBuilder('c')
                    ->where('c.status = :status')
                    ->andwhere('c.User = :user')
                    ->setParameter('user', $user)
                    ->setParameter('status', 'En maintenance')
                    ->getQuery()
                    ->getResult();
    }
    public function CarByUser(User $user) {
        return $this->createQueryBuilder('c')
                    ->andwhere('c.User = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getResult();
    }
      
}
