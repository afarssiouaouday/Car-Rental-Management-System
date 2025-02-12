<?php

namespace App\Controller\Admin;

use App\Entity\CarMaintenance;
use App\Form\CarMaintenceType;
use App\Repository\CarMaintenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/carmaintenance', name: 'admin.carmaintenance')]
#[IsGranted('ROLE_MANAGER')]
class CarMaintenanceController extends AbstractController
{
    #[Route('/', name: '.show')]
    public function index(CarMaintenanceRepository $carMaintenanceRepository): Response
    {
        $user = $this->getUser();
        $carMaintenances = $carMaintenanceRepository->findByUser($user);

        return $this->render('admin/car_maintenance/show.html.twig', [
            'carMaintenances' => $carMaintenances,
        ]);
    }

    #[Route('/details/{id}', name: '.detail')]
    public function detail($id,CarMaintenanceRepository $carMaintenanceRepository): Response
    {
        $carMaintenances = $carMaintenanceRepository->find($id);

        return $this->render('admin/car_maintenance/detail.html.twig', [
            'carMaintenance' => $carMaintenances,
        ]);
    }

    #[Route('/create', name: '.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $carMaintenances = new CarMaintenance();
        $form = $this->createForm(CarMaintenceType::class, $carMaintenances, [
            'user' => $user,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($carMaintenances);
            $em->flush();
            $this->addFlash('success', 'l\'entretien de voiture a bien été créée.');
            
            return $this->redirectToRoute('admin.carmaintenance.show');
    }

    return $this->render('admin/car_maintenance/create.html.twig', [
        'form' => $form->createView(), 
    ]);
    }

    #[Route('/{id}/edit',name:'.edit',methods:['GET', 'POST'],requirements:['id'=>Requirement::DIGITS])]
    public function edit(CarMaintenance $carMaintenance,Request $request,EntityManagerInterface $em,FormFactoryInterface $formFactory,int $id){

        $user = $this->getUser();
        $car = $em->getRepository(CarMaintenance::class)->find($id);
        if (!$car || $car->getcar()->getUser() !== $user) {
            return $this->redirectToRoute("admin.carmaintenance.show");
            
        }
        $form=$formFactory->create(CarMaintenceType::class,$carMaintenance,
        [
            'user' => $user,
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'l\'entretien modifiee');
                
            return $this->redirectToRoute('admin.carmaintenance.show');
        }
        return $this->render('admin/car_maintenance/edit.html.twig',[
            'carMaintenance' => $carMaintenance,
            'form'=>$form
        ]);
    }

    #[Route('/{id}/delete',name:'.delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])]
    public function dalete(CarMaintenance $carMaintenance,EntityManagerInterface $em) {
        $em->remove($carMaintenance);
        $em->flush();
        $this->addFlash('success','l \'entrentien  a bien ete supprimee');
        
        return $this->redirectToRoute('admin.carmaintenance.show');
    }

}
