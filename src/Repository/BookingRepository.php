<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
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

    public function UpcomingBookings(User $user) {
        $today = new \DateTime();
        return   $this->createQueryBuilder('b')
            ->innerJoin('b.car', 'c') 
            ->innerJoin('b.customer', 'cu')
            ->where('b.start_date >= :today')
            ->andWhere('c.User = :user')
            ->andWhere('cu.User = :user')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->orderBy('b.start_date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function PastBookings(User $user) {
        $today = new \DateTime();
        return  $this->createQueryBuilder('b')
            ->innerJoin('b.car', 'c') 
            ->innerJoin('b.customer', 'cu') 
            ->where('b.end_date <= :today')
            ->andWhere('c.User = :user')
            ->andWhere('cu.User = :user')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->orderBy('b.end_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function NowBookings(User $user){
        $today = new \DateTime();
        return $this->createQueryBuilder('b')
                    ->innerJoin('b.car', 'c') 
                    ->innerJoin('b.customer', 'cu') 
                    ->andWhere('b.start_date <= :today')
                    ->andWhere('b.end_date > :today')
                    ->andWhere('c.User = :user')
                    ->andWhere('cu.User = :user')
                    ->setParameter('user', $user)
                    ->setParameter('today', $today)
                    ->getQuery()
                    ->getResult();
    }

    public function findByUser(User $user){
        $today = new \DateTime();
        return $this->createQueryBuilder('b')
                    ->innerJoin('b.car', 'c') 
                    ->innerJoin('b.customer', 'cu') 
                    ->andWhere('c.User = :user')
                    ->andWhere('cu.User = :user')
                    ->setParameter('user', $user)
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
    public function getMonthlyBookings(User $user): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT MONTH(b.start_date) as month, COUNT(b.id) as count
            FROM Booking b
            INNER JOIN Customer c ON b.Customer_id = c.id
            WHERE YEAR(b.start_date) = YEAR(CURRENT_DATE()) 
            AND c.user_id = :user
            GROUP BY month
            ORDER BY month ASC
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['user' => $user->getId()]);

        return $result->fetchAllAssociative();
    }



}
