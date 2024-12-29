<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    //    /**
    //     * @return Booking[] Returns an array of Booking objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Booking
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function UpcomingBookings() {
        $today = new \DateTime();
        return   $this->createQueryBuilder('b')
            ->where('b.start_date >= :today')
            ->setParameter('today', $today)
            ->orderBy('b.start_date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function PastBookings() {
        $today = new \DateTime();
        return  $this->createQueryBuilder('b')
            ->where('b.end_date <= :today')
            ->setParameter('today', $today)
            ->orderBy('b.end_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function NowBookings() {
        $today = new \DateTime();
        return $this->createQueryBuilder('b')
                    ->andWhere('b.start_date <= :today')
                    ->andWhere('b.end_date > :today')
                    ->setParameter('today', $today)
                    ->getQuery()
                    ->getResult();
    }
    

    public function NowBookingsCar() {
        $today = new \DateTime();

        return $this->createQueryBuilder('b')
                    ->innerJoin('b.car', 'c') 
                    ->addSelect('c')
                    ->andWhere('b.start_date <= :today')
                    ->andWhere('b.end_date > :today')
                    ->setParameter('today', $today)
                    ->getQuery()
                    ->getResult();
    }
    
}
