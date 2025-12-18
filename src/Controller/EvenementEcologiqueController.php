<?php

namespace App\Controller;

use App\Entity\EvenementEcologique;
use App\Form\EvenementEcologiqueType;
use App\Repository\EvenementEcologiqueRepository;
use App\Service\TranslationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenementecologique')]
final class EvenementEcologiqueController extends AbstractController
{
    #[Route(name: 'app_evenement_ecologique_index', methods: ['GET'])]
    public function index(Request $request, EvenementEcologiqueRepository $evenementEcologiqueRepository): Response
    {
        $criteria = [
            'title' => $request->query->get('title', ''),
            'categorie' => $request->query->get('categorie', ''),
            'lieu' => $request->query->get('lieu', ''),
            'date' => $request->query->get('date', ''),
        ];

        $filteredCriteria = array_filter($criteria, fn($v) => $v !== null && $v !== '');
        
        // If no filters, get all events, otherwise search
        $events = empty($filteredCriteria) 
            ? $evenementEcologiqueRepository->findAll() 
            : $evenementEcologiqueRepository->search($filteredCriteria);

        return $this->render('evenement_ecologique/index.html.twig', [
            'evenement_ecologiques' => $events,
            'filters' => $criteria,
        ]);
    }

    #[Route('/new', name: 'app_evenement_ecologique_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenementEcologique = new EvenementEcologique();
        $form = $this->createForm(EvenementEcologiqueType::class, $evenementEcologique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenementEcologique);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_ecologique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement_ecologique/new.html.twig', [
            'evenement_ecologique' => $evenementEcologique,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_ecologique_show', methods: ['GET'])]
    public function show(EvenementEcologique $evenementEcologique): Response
    {
        return $this->render('evenement_ecologique/show.html.twig', [
            'evenement_ecologique' => $evenementEcologique,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_ecologique_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EvenementEcologique $evenementEcologique, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementEcologiqueType::class, $evenementEcologique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_ecologique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement_ecologique/edit.html.twig', [
            'evenement_ecologique' => $evenementEcologique,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_ecologique_delete', methods: ['POST'])]
    public function delete(Request $request, EvenementEcologique $evenementEcologique, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenementEcologique->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenementEcologique);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_ecologique_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/statistics', name: 'app_evenement_ecologique_statistics', methods: ['GET'])]
    public function statistics(int $id, EvenementEcologiqueRepository $evenementEcologiqueRepository): Response
    {
        $evenementEcologique = $evenementEcologiqueRepository->find($id);
        if (!$evenementEcologique) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        $participations = $evenementEcologique->getParticipations();
        
        $stats = [
            'total' => count($participations),
            'inscrit' => 0,
            'confirme' => 0,
            'present' => 0,
            'absent' => 0,
            'annule' => 0,
            'tauxPresence' => 0,
            'tauxRemplissage' => 0,
        ];

        foreach ($participations as $participation) {
            $statut = $participation->getStatut();
            if ($statut) {
                switch ($statut) {
                    case \App\Entity\StatutParticipation::INSCRIT:
                        $stats['inscrit']++;
                        break;
                    case \App\Entity\StatutParticipation::CONFIRME:
                        $stats['confirme']++;
                        break;
                    case \App\Entity\StatutParticipation::PRESENT:
                        $stats['present']++;
                        break;
                    case \App\Entity\StatutParticipation::ABSENT:
                        $stats['absent']++;
                        break;
                    case \App\Entity\StatutParticipation::ANNULE:
                        $stats['annule']++;
                        break;
                }
            }
        }

        // Calculate rates
        $activeParticipations = $stats['total'] - $stats['annule'];
        if ($activeParticipations > 0) {
            $stats['tauxPresence'] = round(($stats['present'] / $activeParticipations) * 100, 2);
        }
        if ($evenementEcologique->getCapaciteMax() > 0) {
            $stats['tauxRemplissage'] = round(($activeParticipations / $evenementEcologique->getCapaciteMax()) * 100, 2);
        }

        return $this->render('evenement_ecologique/statistics.html.twig', [
            'evenement_ecologique' => $evenementEcologique,
            'stats' => $stats,
            'participations' => $participations,
        ]);
    }

    #[Route('/{id}/export-participants', name: 'app_evenement_ecologique_export', methods: ['GET'])]
    public function exportParticipants(int $id, EvenementEcologiqueRepository $evenementEcologiqueRepository): Response
    {
        $evenementEcologique = $evenementEcologiqueRepository->find($id);
        if (!$evenementEcologique) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        $participations = $evenementEcologique->getParticipations();
        
        $filename = sprintf(
            'participants_%s_%s.csv',
            preg_replace('/[^a-z0-9]/i', '_', $evenementEcologique->getTitre()),
            date('Y-m-d')
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');
        
        // BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($output, [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Date Inscription',
            'Statut',
        ], ';');

        // Data
        foreach ($participations as $participation) {
            $user = $participation->getIdUser();
            if ($user) {
                fputcsv($output, [
                    $participation->getId(),
                    $user->getNom(),
                    $user->getPrenom(),
                    $user->getEmail(),
                    $user->getTelephone(),
                    $participation->getDateInscription() ? $participation->getDateInscription()->format('d/m/Y H:i') : '',
                    $participation->getStatut() ? $participation->getStatut()->label() : 'Non défini',
                ], ';');
            }
        }

        fclose($output);
        
        $response->setContent(ob_get_clean());
        return $response;
    }

    #[Route('/{id}/translate', name: 'app_evenement_ecologique_translate', methods: ['GET'])]
    public function translate(
        EvenementEcologique $evenementEcologique, 
        Request $request,
        TranslationService $translationService
    ): JsonResponse {
        $lang = $request->query->get('lang', 'en');
        
        $translated = $translationService->translateEvent($evenementEcologique, $lang);
        
        return $this->json([
            'success' => true,
            'original' => [
                'titre' => $evenementEcologique->getTitre(),
                'description' => $evenementEcologique->getDescription(),
                'lieu' => $evenementEcologique->getLieu(),
            ],
            'translated' => $translated,
            'language' => $lang,
        ]);
    }
}
