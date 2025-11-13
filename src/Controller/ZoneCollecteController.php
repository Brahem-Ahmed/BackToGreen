<?php

namespace App\Controller;

use App\Entity\ZoneCollecte;
use App\Form\ZoneCollecteType;
use App\Repository\ZoneCollecteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/zone/collecte')]
final class ZoneCollecteController extends AbstractController
{
    #[Route(name: 'app_zone_collecte_index', methods: ['GET'])]
    public function index(ZoneCollecteRepository $zoneCollecteRepository): Response
    {
        return $this->render('zone_collecte/index.html.twig', [
            'zone_collectes' => $zoneCollecteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_zone_collecte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $zoneCollecte = new ZoneCollecte();
        $form = $this->createForm(ZoneCollecteType::class, $zoneCollecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zoneCollecte);
            $entityManager->flush();

            return $this->redirectToRoute('app_zone_collecte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('zone_collecte/new.html.twig', [
            'zone_collecte' => $zoneCollecte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_zone_collecte_show', methods: ['GET'])]
    public function show(ZoneCollecte $zoneCollecte): Response
    {
        return $this->render('zone_collecte/show.html.twig', [
            'zone_collecte' => $zoneCollecte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_zone_collecte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ZoneCollecte $zoneCollecte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ZoneCollecteType::class, $zoneCollecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_zone_collecte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('zone_collecte/edit.html.twig', [
            'zone_collecte' => $zoneCollecte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_zone_collecte_delete', methods: ['POST'])]
    public function delete(Request $request, ZoneCollecte $zoneCollecte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$zoneCollecte->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($zoneCollecte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_zone_collecte_index', [], Response::HTTP_SEE_OTHER);
    }
}
