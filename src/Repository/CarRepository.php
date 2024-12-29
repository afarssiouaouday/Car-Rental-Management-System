<?php

namespace App\Repository;

use App\Entity\Car;
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
      public function carAsvailable(){
        return $this->createQueryBuilder('c')
          ->where('c.status = :available')
          ->setParameter('available', 'Disponible')
          ->getQuery()
          ->getResult();
      }

      public function findAvailableCars(DateTimeInterface $startDate, DateTimeInterface $endDate, ?int $excludeBookingId = null)
      {
          $qb = $this->createQueryBuilder('c')
              ->where('c.status = :status')
              ->setParameter('status', 'Disponible');
  
          // Sous-requête pour exclure les voitures déjà réservées
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
  
          // Exclure la réservation en cours d'édition si nécessaire
          if ($excludeBookingId) {
              $qb->andWhere('NOT EXISTS (
                  SELECT b 
                  FROM App\Entity\Booking b 
                  WHERE b.car = c 
                  AND b.id = :bookingId
              )')
              ->setParameter('bookingId', $excludeBookingId);
          }
  
          return $qb->getQuery()->getResult();
      }

      public function carMaintenance() {
        return $this->createQueryBuilder('c')
                    ->where('c.status = :status')
                    ->setParameter('status', 'En maintenance')
                    ->getQuery()
                    ->getResult();
    }
      
}
