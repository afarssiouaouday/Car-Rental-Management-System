<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('admin/payment', name: 'admin_payment')]
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
