<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\BookingRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/customer', name: 'admin.customer')]
#[IsGranted('ROLE_MANAGER')]
class CustomerController extends AbstractController
{
    #[Route('/', name: '.show')]
    public function show(CustomerRepository $customerRepository): Response
    {
        $customers = $customerRepository->findByUser($this->getUser());

        return $this->render('admin/customer/show.html.twig', [
            "customers" => $customers,
        ]);
    }

    #[Route('/details/{id}', name: '.detail')]
    public function detail($id , CustomerRepository $customerRepository, BookingRepository $bookingRepository): Response
    {
        $customer = $customerRepository->find($id);
        $user = $this->getUser();
        if (!$customer || $customer->getUser() !== $user) {
            return $this->redirectToRoute("admin.customer.show");
            
        }

        $reservations = $bookingRepository->findBy(['customer' => $customer]);

        return $this->render('admin/customer/detail.html.twig', [
            'customer' => $customer,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/create', name: '.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customer->setUser($this->getUser());
            $em->persist($customer);
            $em->flush();
            $this->addFlash('success', 'Le client a bien  été créée.');
            
            return $this->redirectToRoute('admin.customer.show');
    }

    return $this->render('admin/customer/create.html.twig', [
        'form' => $form,
    ]);
    }

    #[Route('/{id}/edit',name:'.edit',methods:['GET', 'POST'],requirements:['id'=>Requirement::DIGITS])]
    public function edit(Customer $customer,Request $request,EntityManagerInterface $em,FormFactoryInterface $formFactory,CustomerRepository $customerRepository,int $id){

        $customer = $customerRepository->find($id);
        $user = $this->getUser();
        if (!$customer || $customer->getUser() !== $user) {
            return $this->redirectToRoute("admin.customer.show");
            
        }
        $form=$formFactory->create(CustomerType::class,$customer);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'voiture modifiee');

            return $this->redirectToRoute('admin.customer.show');
        }

        return $this->render('admin/customer/edit.html.twig',[
            'customer'=>$customer,
            'form'=>$form
        ]);
    }

    #[Route('/{id}/delete',name:'.delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])]
    public function dalete(Customer $customer,EntityManagerInterface $em) {
        $em->remove($customer);
        $em->flush();
        $this->addFlash('success','Le client a bien été supprimé.');
        
        return $this->redirectToRoute('admin.customer.show');
    }
}

