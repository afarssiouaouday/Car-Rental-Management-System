<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/payment', name: 'admin_payment')]
#[IsGranted('ROLE_MANAGER')]
class PaymentController extends AbstractController
{
    #[Route('/', name: '_show')]
    public function show(PaymentRepository $paymentRepository): Response
    {
        $payments = $paymentRepository->findAll();


        return $this->render('admin/payment/show.html.twig', [
            'payemnts' => $payments,
        ]);
    }

    
}
