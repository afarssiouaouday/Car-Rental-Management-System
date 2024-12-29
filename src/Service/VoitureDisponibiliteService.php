<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class VoitureDisponibiliteService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function getVoituresDisponibles(\DateTime $startDate, \DateTime $endDate): array
    {
        // On utilise le QueryBuilder pour une requête optimisée
        $qb = $this->entityManager->createQueryBuilder();
        
        return $qb->select('v')
            ->from('App\Entity\Voiture', 'v')
            ->leftJoin('App\Entity\Reservation', 'r', 'WITH', 
                $qb->expr()->andX(
                    $qb->expr()->eq('r.voiture', 'v.id'),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->lte('r.startDate', ':endDate'), // La réservation commence avant la fin
                            $qb->expr()->gte('r.endDate', ':startDate')  // La réservation se termine après le début
                        )
                    )
                )
            )
            ->where('r.id IS NULL') // Assure qu'il n'y a pas de réservation qui chevauche
            ->setParameter('startDate', $startDate) // Paramètre de la date de début
            ->setParameter('endDate', $endDate)     // Paramètre de la date de fin
            ->getQuery()
            ->getResult(); // Exécution de la requête et récupération des résultats
    }
}
