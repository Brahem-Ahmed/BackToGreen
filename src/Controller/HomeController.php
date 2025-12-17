<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('frontoffice/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
public function logout(): void
{
    // Cette méthode reste vide - Symfony gère le logout automatiquement
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
}
}