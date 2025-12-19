<?php

namespace App\Controller\Front;

use App\Repository\ZoneCollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/map', name: 'app_front_map')]
class MapController extends AbstractController
{
    #[Route('', name: '')]
    public function index(ZoneCollecteRepository $zoneRepo): Response
    {
        $zones = $zoneRepo->findAll();

        return $this->render('front/map/index.html.twig', [
            'zones' => $zones,
        ]);
    }
}
