<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/admin/mon-compte', name: 'admin_account_show')]
    public function show(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérification de sécurité (optionnel)
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        return $this->render('admin/account/show.html.twig', [
            'user' => $user,
        ]);
    }
}
