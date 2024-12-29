<?php 
namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\CarMaintenance;
use App\Entity\Customer;
use App\Repository\BookingRepository;
use App\Repository\CarMaintenanceRepository;
use App\Repository\CarRepository;
use App\Repository\PaymentRepository;
use App\Service\VoitureDisponibiliteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\CardSchemeValidator;

class DashboardController extends AbstractController
{
    private VoitureDisponibiliteService $voitureDisponibiliteService;

    // Constructor injection of VoitureDisponibiliteService
    public function __construct(VoitureDisponibiliteService $voitureDisponibiliteService)
    {
        $this->voitureDisponibiliteService = $voitureDisponibiliteService;
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(
        CarRepository $carRepository,
        PaymentRepository $paymentRepository,
        BookingRepository $bookingRepository,
        CarMaintenanceRepository $carMaintenanceRepository
    ): Response
    {
        
        $this->voitureDisponibiliteService->checkCarAvailability();
        
        $today = new \DateTime();
        $carsCount = $carRepository->count();
        $paymentsCount = $paymentRepository->count();
        $carsMaintenances = $carRepository->carMaintenance();
        $carsMaintenancesCount = count($carsMaintenances);


        $pastBooking = $bookingRepository->PastBookings();
        $pastBookingsCount = count($pastBooking);
        $upcomingBookings = $bookingRepository->UpcomingBookings();
        $upcomingBookingscount = count($upcomingBookings);
        $nowBookings = $bookingRepository->NowBookings();
        $nowBookingsCount = count($nowBookings);


        
        $carAvailable = $carRepository->carAsvailable();
        $carAvailableCount = count($carAvailable);
    
        
        return $this->render('admin/dashboard/index.html.twig',[
            "upcomingBookingscount" => $upcomingBookingscount,
            "pastBookingsCount" => $pastBookingsCount,
            "nowBookingsCount" =>   $nowBookingsCount,
            "carsCount" => $carsCount,
            "paymentsCount" => $paymentsCount,
            "carsMaintenancesCount" => $carsMaintenancesCount,
            "carAvailableCount" => $carAvailableCount,
        ]);
    }
}
