<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use App\Service\ParticipationPdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participation')]
final class ParticipationController extends AbstractController
{
    #[Route(name: 'app_participation_index', methods: ['GET'])]
    public function index(Request $request, ParticipationRepository $participationRepository): Response
    {
        $criteria = [
            'user' => $request->query->get('user', ''),
            'event' => $request->query->get('event', ''),
            'statut' => $request->query->get('statut', ''),
            'date' => $request->query->get('date', ''),
        ];

        $filteredCriteria = array_filter($criteria, fn($v) => $v !== null && $v !== '');
        
        // If no filters, get all participations, otherwise search
        $participations = empty($filteredCriteria) 
            ? $participationRepository->findAll() 
            : $participationRepository->search($filteredCriteria);

        return $this->render('participation/index.html.twig', [
            'participations' => $participations,
            'filters' => $criteria,
        ]);
    }

    #[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_participation_pdf', methods: ['GET'])]
    public function generatePdf(Participation $participation, ParticipationPdfService $pdfService): Response
    {
        $html = $pdfService->generatePdfHtml($participation);

        $response = new Response($html);
        $response->headers->set('Content-Type', 'text/html; charset=utf-8');
        $response->headers->set('Content-Disposition', 'inline; filename="participation-' . $participation->getId() . '.html"');

        return $response;
    }

    #[Route('/{id}/ticket', name: 'app_participation_ticket', methods: ['GET'])]
    public function ticket(Participation $participation): Response
    {
        return $this->render('participation/ticket.html.twig', [
            'participation' => $participation,
        ]);
    }
}
