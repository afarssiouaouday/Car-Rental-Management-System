<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\Car;
use App\Entity\CarMaintenance;
use App\Form\CarType;
use App\Repository\BookingRepository;
use App\Repository\CarMaintenanceRepository;
use App\Repository\CarRepository;
use App\Service\VoitureDisponibiliteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Maker\Common\EntityIdTypeEnum;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('admin/car', name: 'admin.car')]
class CarController extends AbstractController
{

    private VoitureDisponibiliteService $voitureDisponibiliteService;

    public function __construct(VoitureDisponibiliteService $voitureDisponibiliteService)
    {
        $this->voitureDisponibiliteService = $voitureDisponibiliteService;
    }

    #[Route('/', name: '.show')]
    public function index(CarRepository $carRepository): Response
    {
        $this->voitureDisponibiliteService->checkCarAvailability();
        $cars = $carRepository->findAll();

        return $this->render('admin/car/show.html.twig', [
            'cars' => $cars,
            'cartype' => "Voitures",
        ]);
    }


    #[Route('/details/{id}', name: '.detail')]
    public function detail($id,CarRepository $carRepository): Response
        {
            $car = $carRepository ->find($id);

            return $this->render('admin/car/detail.html.twig', [
                'car' => $car,
            ]);
        }

    #[Route('/create', name: '.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $car->setStatus("disponible");
            $em->persist($car);
            $em->flush();
            $this->addFlash('success', 'La voiture a bien été créée.');
            
            return $this->redirectToRoute('admin.car.show');
    }

    return $this->render('admin/car/create.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/{id}/edit',name:'.edit',methods:['GET', 'POST'],requirements:['id'=>Requirement::DIGITS])]
    public function edit(Car $car,Request $request,EntityManagerInterface $em,FormFactoryInterface $formFactory){
        $form=$formFactory->create(CarType::class,$car);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'voiture modifiee');
                
            return $this->redirectToRoute('admin.car.show');
        }
        return $this->render('admin/car/edit.html.twig',[
            'car'=>$car,
            'form'=>$form
        ]);
    }

    #[Route('/{id}/delete',name:'.delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])]
    public function dalete(Car $car,EntityManagerInterface $em) {
        $em->remove($car);
        $em->flush();
        $this->addFlash('success','la recette a bien ete supprimee');
        
        return $this->redirectToRoute('admin.car.show');
    }



    #[Route('/Avalaible', name: '.Avalaible.show')]
    public function show_Avalibale(CarRepository $carRepository): Response
    {
        $cars = $carRepository->carAsvailable();

        return $this->render('admin/car/show.html.twig', [
            'cars' => $cars,
            'cartype' => "voitures disponibles",
        ]);
    }


    #[Route('/Avalaible/{id}/edit',name:'.Avalaible.edit',methods:['GET', 'POST'],requirements:['id'=>Requirement::DIGITS])]
    public function edit_car_avalaible(Car $car,Request $request,EntityManagerInterface $em,FormFactoryInterface $formFactory){
        $form=$formFactory->create(CarType::class,$car);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'voiture modifiee');
            return $this->redirectToRoute('admin.car.Avalaible.show');
        }
        return $this->render('admin/car/CarAvalaible/edit.html.twig',[
            'car'=>$car,
            'form'=>$form
        ]);
    }

    #[Route('/Avalaible/{id}/delete',name:'.Avalaible.delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])]
    public function remove_avalaible_car(Car $car,EntityManagerInterface $em) {
        $em->remove($car);
        $em->flush();
        $this->addFlash('success','la recette a bien ete supprimee');
        
        return $this->redirectToRoute('admin.car.Avalaible.show');
    }

    #[Route('/InMaintenance', name: '.inmaintenance')]
    public function show_inmaintenance(CarRepository $car): Response
    {
        $cars = $car->carMaintenance();
        

        return $this->render('admin/car/show.html.twig', [
            'cars' => $cars,
            'cartype' => "voitures en maintenance",
        ]);
    }
    
    #[Route('/{id}/bookings', name: '.bookings', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function car_bookings($id,CarRepository $carRepository,Request $request, EntityManagerInterface $em, FormFactoryInterface $formFactory,BookingRepository $bookingrepo)
    {
        $now = new \DateTimeImmutable();
        $bookings = $bookingrepo
        ->createQueryBuilder('b')
        ->andWhere('b.start_date <= :now AND b.end_date >= :now AND b.car = :id') 
        ->orWhere('b.start_date >= :now AND b.car = :id')  
        ->setParameter('id', $id)
        ->setParameter('now', $now)
        ->orderBy('b.start_date', 'ASC')
        ->getQuery()
        ->getResult();

        $car = $carRepository->find($id);

        return $this->render('admin/car/bookings.html.twig', [
            'car' => $car,
            'bookings' => $bookings,
        ]);
    }



    

}
