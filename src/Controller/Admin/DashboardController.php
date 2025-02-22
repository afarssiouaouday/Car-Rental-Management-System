<?php 
namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\CarMaintenance;
use App\Entity\Customer;
use App\Repository\BookingRepository;
use App\Repository\CarMaintenanceRepository;
use App\Repository\CarRepository;
use App\Repository\PaymentRepository;
use App\Service\RoleCheckerService;
use App\Service\VoitureDisponibiliteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\CardSchemeValidator;

class DashboardController extends AbstractController
{
    private VoitureDisponibiliteService $voitureDisponibiliteService;

    // Constructor injection of VoitureDisponibiliteService
    public function __construct(VoitureDisponibiliteService $voitureDisponibiliteService)
    {
        $this->voitureDisponibiliteService = $voitureDisponibiliteService;
        
    }

    #[Route('/', name: 'admin')]
    public function racine(): Response{
        if($this->getUser()==NULL)
        return $this->redirect('/login');
        else return $this->redirect('/admin/dashboard');
    
    }

    #[Route('/verify', name: 'app_home')]
    public function verify(): Response{
        return $this->redirect('/login');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(
        CarRepository $carRepository,
        PaymentRepository $paymentRepository,
        BookingRepository $bookingRepository,
        CarMaintenanceRepository $carMaintenanceRepository,
        RoleCheckerService $roleChecker
    ): Response
    {


        $redirect = $roleChecker->checkManagerRole();
        if ($redirect) {
            return $redirect; // Redirige si l'utilisateur n'a pas le bon rôle
        }

        if($this->getUser()->getRoles())
        $this->voitureDisponibiliteService->checkCarAvailability();
        
        $today = new \DateTime();
        $user = $this->getUser();
        $carsCount = count($carRepository->CarByUser($user));
        $carsMaintenances = $carRepository->carMaintenance($user);
        $carsMaintenancesCount = count($carsMaintenances);


        $pastBooking = $bookingRepository->PastBookings($user);
        $pastBookingsCount = count($pastBooking);
        $upcomingBookings = $bookingRepository->UpcomingBookings($user);
        $upcomingBookingscount = count($upcomingBookings);
        $nowBookings = $bookingRepository->NowBookings($user);
        $nowBookingsCount = count($nowBookings);


        
        $carAvailable = $carRepository->carAsvailable($user);
        $carAvailableCount = count($carAvailable);

        $monthlyBookings = $bookingRepository->getMonthlyBookings($user);
        $months = [];
        $rentals = [];

        for ($i = 1; $i <= 12; $i++) {
            // Récupérer l'abréviation en français pour le mois $i
            $months[] = $this->getFrenchMonthAbbreviation($i);
            // Valeur par défaut 0 pour chaque mois
            $rentals[$i] = 0;
        }

        foreach ($monthlyBookings as $booking) {
            // $booking['month'] doit correspondre à un nombre entre 1 et 12
            $monthNumber = (int)$booking['month'];
            // Mettre à jour la valeur pour ce mois
            $rentals[$monthNumber] = (int)$booking['count'];
        }
        
        // Re-indexer le tableau $rentals si nécessaire (les clés seront perdues et remplacées par des index numériques de 0 à 11)
        $chartData = [
            'months'  => $months,
            'rentals' => array_values($rentals),
        ];
        
        return $this->render('admin/dashboard/index.html.twig',[
            "upcomingBookingscount" => $upcomingBookingscount,
            "pastBookingsCount" => $pastBookingsCount,
            "nowBookingsCount" =>   $nowBookingsCount,
            "carsCount" => $carsCount,
            "carsMaintenancesCount" => $carsMaintenancesCount,
            "carAvailableCount" => $carAvailableCount,
            'chartData' => $chartData,
        ]
    );
    }
    private function getFrenchMonthAbbreviation(int $monthNumber): string
{
    $months = [
        1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 
        4 => 'Avr', 5 => 'Mai', 6 => 'Juin',
        7 => 'Juil', 8 => 'Aoû', 9 => 'Sep',
        10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
    ];
    return $months[$monthNumber] ?? '';
}
}
