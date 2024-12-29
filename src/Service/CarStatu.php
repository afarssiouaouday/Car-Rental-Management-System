<?php
namespace App\Service;

use App\Repository\BookingRepository;
use App\Repository\CarMaintenanceRepository;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;

class VoitureDisponibiliteService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CarRepository $carRepository,
        private BookingRepository $bookingRepository,
        private CarMaintenanceRepository $carMaintenanceRepository,
    ) {}

    public function checkCarAvailability()
    {
        $cars = $this->carRepository->findAll();
        $carsNow = $this->bookingRepository->NowBookingsCar(); // Make sure this method exists
        $carsMaintenance = $this->carMaintenanceRepository->findInProgressMaintenances(); // Make sure this method exists

        foreach ($cars as $car) {
            $isBooked = false;

            // Check if the car is booked
            foreach ($carsNow as $booking) {
                if ($booking->getCar() === $car) {
                    $isBooked = true;
                    break;
                }
            }

            if ($isBooked) {
                $car->setStatus('indisponible');
            } else {
                // Check if the car is under maintenance
                $isInMaintenance = false;

                foreach ($carsMaintenance as $maintenance) {
                    if ($maintenance->getCar() === $car) {
                        $isInMaintenance = true;
                        break;
                    }
                }

                if ($isInMaintenance) {
                    $car->setStatus('en maintenance');
                } else {
                    $car->setStatus('disponible');
                }
            }

            // Persist changes for each car
            $this->entityManager->persist($car);
        }

        // Save all changes
        $this->entityManager->flush();
    }
}
