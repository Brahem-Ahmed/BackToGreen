<?php

namespace App\Controller;

use App\Entity\ZoneCollecte;
use App\Form\ZoneCollecteType;
use App\Repository\ZoneCollecteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/front/zone_collecte')]
final class FrontZoneCollecteController extends AbstractController
{
    #[Route('/', name: 'front_zone_index', methods: ['GET'])]
    public function index(Request $request, ZoneCollecteRepository $repo): Response
    {
        $search = $request->query->get('search', '');
        
        if (!empty($search)) {
            $zones = $repo->searchZones($search);
        } else {
            $zones = $repo->findAll();
        }

        return $this->render('frontoffice/zone_collecte/index.html.twig', [
            'zones' => $zones,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'front_zone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $zone = new ZoneCollecte();
        $form = $this->createForm(ZoneCollecteType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($zone);
            $em->flush();

            return $this->redirectToRoute('front_zone_index');
        }

        return $this->render('frontoffice/zone_collecte/new.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'front_zone_show', methods: ['GET'])]
    public function show(ZoneCollecte $zone): Response
    {
        return $this->render('frontoffice/zone_collecte/show.html.twig', [
            'zone' => $zone,
        ]);
    }

    #[Route('/{id}/edit', name: 'front_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ZoneCollecte $zone, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ZoneCollecteType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('front_zone_index');
        }

        return $this->render('frontoffice/zone_collecte/edit.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'front_zone_delete', methods: ['POST'])]
    public function delete(Request $request, ZoneCollecte $zone, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$zone->getId(), $token)) {
            $em->remove($zone);
            $em->flush();
        }

        return $this->redirectToRoute('front_zone_index');
    }

 #[Route('/{id}/qr-code', name: 'front_zone_qr_code', methods: ['GET'])]
public function qrCode(ZoneCollecte $zone): Response
{
    $url = $this->generateUrl('front_zone_show', [
        'id' => $zone->getId()
    ], UrlGeneratorInterface::ABSOLUTE_URL);
    
    // Utiliser un service externe fiable
    $qrServiceUrl = 'https://api.qrserver.com/v1/create-qr-code/';
    $qrServiceUrl .= '?size=300x300&margin=10&format=png&data=' . urlencode($url);
    
    // Récupérer l'image
    $imageContent = @file_get_contents($qrServiceUrl);
    
    if ($imageContent === false) {
        // En cas d'erreur, créer une image d'erreur simple
        $imageContent = $this->createErrorImage($zone->getNom());
    }
    
    return new Response(
        $imageContent,
        Response::HTTP_OK,
        [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="qr-zone-' . $zone->getId() . '.png"'
        ]
    );
}

private function createErrorImage(string $zoneName): string
{
    // Créer une simple image d'erreur
    $image = imagecreate(300, 300);
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 255, 0, 0);
    
    imagefilledrectangle($image, 0, 0, 300, 300, $backgroundColor);
    imagestring($image, 5, 50, 140, 'QR Code Error', $textColor);
    imagestring($image, 3, 30, 160, 'Zone: ' . substr($zoneName, 0, 20), $textColor);
    
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    imagedestroy($image);
    
    return $imageData;
}

    #[Route('/search/ajax', name: 'front_zone_search_ajax', methods: ['GET'])]
    public function searchAjax(Request $request, ZoneCollecteRepository $repo): JsonResponse
    {
        $search = $request->query->get('q', '');
        
        if (!empty($search)) {
            $zones = $repo->searchZones($search);
        } else {
            $zones = $repo->findAll();
        }
        
        $zonesArray = [];
        foreach ($zones as $zone) {
            $zonesArray[] = [
                'id' => $zone->getId(),
                'nom' => $zone->getNom(),
                'adresse' => $zone->getAdresse(),
                'typeDechet' => $zone->getTypeDechet()->label(),
                'capacite' => $zone->getCapacite(),
                'showUrl' => $this->generateUrl('front_zone_show', ['id' => $zone->getId()]),
                'qrCodeUrl' => $this->generateUrl('front_zone_qr_code', ['id' => $zone->getId()]),
            ];
        }
        
        return $this->json([
            'zones' => $zonesArray,
            'count' => count($zones),
            'search' => $search
        ]);
    }
}