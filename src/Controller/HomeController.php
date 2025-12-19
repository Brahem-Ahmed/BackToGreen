<?php

namespace App\Controller;

use App\Repository\EvenementEcologiqueRepository;
use App\Repository\GroupeRepository;
use App\Repository\UserRepository;
use App\Repository\ZoneCollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        EvenementEcologiqueRepository $evenementRepository,
        ZoneCollecteRepository $zoneCollecteRepository
    ): Response
    {
        // Get upcoming events (limit to 4 for homepage)
        $upcomingEvents = $evenementRepository->findAll();
        
        // Get featured collection zones (limit to 3)
        $featuredZones = $zoneCollecteRepository->findBy([], ['id' => 'DESC'], 3);
        
        // Count active campaigns (groupes)
        $activeCampaigns = $groupeRepository->count([]);
        
        // Count volunteers (users)
        $volunteers = $userRepository->count([]);
        
        // Count events organized
        $eventsOrganized = $evenementRepository->count([]);
        
        // Count unique countries
        $countries = 1;
        
        return $this->render('frontoffice/home/index.html.twig', [
            'activeCampaigns' => $activeCampaigns,
            'volunteers' => $volunteers,
            'eventsOrganized' => $eventsOrganized,
            'countries' => $countries,
            'upcomingEvents' => $upcomingEvents,
            'featuredZones' => $featuredZones,
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
public function logout(): void
{
    // Cette méthode reste vide - Symfony gère le logout automatiquement
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
}
}