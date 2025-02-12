<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\Car;
use App\Entity\Payment;
use App\Entity\User;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\CarRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/booking', name: 'admin_booking')]
#[IsGranted('ROLE_MANAGER')]
class BookingController extends AbstractController
{

    #[Route('/', name: '_show')]
    public function show_(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser(); 
        $bookings = $bookingRepository->findByUser($user);
        $date = new \DateTimeImmutable();

        return $this->render('admin/Booking/show.html.twig',[
            'bookings' => $bookings,
            'bookingtype' => 'Réservations',
            'date' => $date,
        ]);
    }


    #[Route('/upcomingbookings', name: '_show_upcomingbookings')]
    public function show_upcomingbookings(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser(); 
        $bookings = $bookingRepository->UpcomingBookings($user);
        $date = new \DateTimeImmutable();

        return $this->render('admin/Booking/show.html.twig',[
            'bookings' => $bookings,
            'bookingtype' => 'Réservations à venir',
            'date' => $date,
        ]);
    }
    
    #[Route('/pastbookings', name: '_show_pastbookings')]
    public function show_pastbookings(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser(); 
        $bookings = $bookingRepository->PastBookings($user);
        $date = new \DateTimeImmutable();

        return $this->render('admin/Booking/show.html.twig',[
            'bookings' => $bookings,
            'bookingtype' => 'Réservations Passeés',
            'date' => $date,
        ]);
    }

    #[Route('/nowbookings', name: '_show_nowbookings')]
    public function show_nowbookings(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser(); 
        $bookings = $bookingRepository->NowBookings($user);
        $date = new \DateTimeImmutable();

        return $this->render('admin/Booking/show.html.twig',[
            'bookings' => $bookings,
            'bookingtype' => 'Réservations actual',
            'date' => $date,
        ]);
    }

    #[Route('/detail/{id}', name: '_detail')]
    public function detail($id, BookingRepository $bookingRepository): Response
    {
        $booking = $bookingRepository->find($id);
        $user = $this->getUser();

        if (!$booking || $booking->getCustomer()->getUser() !== $user) {
            return $this->redirectToRoute("admin_booking_show");
            
        }
        
        return $this->render('admin/Booking/detail.html.twig', [
            'booking' => $booking,
        ]);
    }

    

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        int $id, 
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory
    ): Response {
        $booking = $em->getRepository(Booking::class)->find($id);
        $user = $this->getUser();

        if (!$booking || $booking->getCustomer()->getUser() !== $user) {
            return $this->redirectToRoute("admin_booking_show");
            
        }

        $form = $formFactory->create(BookingType::class, $booking,[
            'user' => $user,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Réservation modifiée avec succès.');

            return $this->redirectToRoute('admin_booking_show');
        }

        return $this->render('admin/Booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/delete',name:'_delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])]
    public function remove_booking(Booking $booking,EntityManagerInterface $em) {
  
        $em->remove($booking);
        $em->flush();
        return $this->redirectToRoute('admin_booking_show');
    }
    

    #[Route('/choose-reservation',name:'_choosereservation',methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function chooseReservation(Request $request, CarRepository $carRepository): Response
    {
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        
        if (!$startDate || !$endDate) {
            $error = 'Veuillez spécifier les dates de début et de fin.';
            return $this->render('admin/booking/choose_reservation.html.twig', [
                'error' => $error,
                'availableCars' => []  
            ]);
        }

        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        $availableCars = $carRepository->findAvailableCars($startDate,$endDate,$this->getUser());

        return $this->render('admin/booking/choose_reservation.html.twig', [
            'availableCars' => $availableCars,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }


    #[Route('/create/{carId}',name:"_creer_reservation")]
    public function createx(int $carId,Request $request,EntityManagerInterface $entityManager): Response {
        
        $user = $this->getUser();
        $car = $entityManager->getRepository(Car::class)->find($carId);

        if (!$car) {
            throw $this->createNotFoundException('Voiture non trouvée.');
        }

        $dateStart = $request->query->get('start');
        $dateEnd = $request->query->get('end');

        if (!$dateStart || !$dateEnd) {
            throw $this->createNotFoundException('Les dates de réservation sont manquantes.');
        }

        $booking = new Booking();
        $booking->setCar($car);
        $booking->setStartDate(new \DateTimeImmutable($dateStart));
        $booking->setEndDate(new \DateTimeImmutable($dateEnd));

        $form = $this->createForm(BookingType::class, $booking, [
            'user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $duration = $booking->getStartDate()->diff($booking->getEndDate());
            $duration = $duration->format('%d'); 
            $pricePerDay = $booking->getCar()->getPrice();
            $totalAmount = $duration * $pricePerDay;
            $date = new DateTimeImmutable();
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'La réservation a bien été créée.');

            return $this->redirectToRoute('admin_booking_show'); 
        }

        return $this->render('admin/booking/create2.html.twig', [
            'form' => $form->createView(),
            'car' => $car,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
        ]);
    }

}
