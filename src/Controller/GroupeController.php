<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeType;
use App\Repository\GroupeRepository;
use App\Service\HashtagGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe')]
final class GroupeController extends AbstractController
{
    #[Route('/api/by-event/{eventId}', name: 'app_groupe_by_event_api', methods: ['GET'])]
    public function getGroupesByEvent(int $eventId, GroupeRepository $groupeRepository): JsonResponse
    {
        $groupes = $groupeRepository->createQueryBuilder('g')
            ->where('g.evenement = :eventId')
            ->setParameter('eventId', $eventId)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($groupes as $groupe) {
            $data[] = [
                'id' => $groupe->getId(),
                'nom' => $groupe->getNom(),
                'description' => $groupe->getDescription(),
                'nombreMembres' => $groupe->getNombreMembres(),
                'activeMembersCount' => $groupe->getActiveMembersCount(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'app_groupe_index', methods: ['GET'])]
    public function index(GroupeRepository $groupeRepository): Response
    {
        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupeRepository->findAll(),
        ]);
    }

    #[Route('/search', name: 'app_groupe_search', methods: ['GET'])]
    public function search(Request $request, GroupeRepository $groupeRepository): Response
    {
        $q = trim($request->query->get('q', ''));

        if ($q === '') {
            $groupes = $groupeRepository->findAll();
        } else {
            $groupes = $groupeRepository->createQueryBuilder('g')
                ->where('g.nom LIKE :search')
                ->orWhere('g.description LIKE :search')
                ->setParameter('search', '%' . $q . '%')
                ->orderBy('g.id', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('groupe/_list_rows.html.twig', [
            'groupes' => $groupes,
        ]);
    }

    #[Route('/new', name: 'app_groupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupe->setDateCreation(new \DateTime());

            $entityManager->persist($groupe);
            $entityManager->flush();

            $this->addFlash('success', 'Groupe créé avec succès !');

            return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe/new.html.twig', [
            'groupe' => $groupe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/hashtags', name: 'app_groupe_hashtags', methods: ['POST'])]
    public function generateHashtags(Request $request, HashtagGenerator $generator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        $hashtags = $generator->generateHashtags($text);

        return new JsonResponse($hashtags);
    }

    #[Route('/{id}', name: 'app_groupe_show', methods: ['GET'])]
    public function show(Groupe $groupe): Response
    {
        return $this->render('groupe/show.html.twig', [
            'groupe' => $groupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Groupe modifié avec succès !');

            return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe/edit.html.twig', [
            'groupe' => $groupe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_delete', methods: ['POST'])]
    public function delete(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $groupe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($groupe);
            $entityManager->flush();

            $this->addFlash('success', 'Groupe supprimé avec succès.');
        }

        return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
    }

    
     #[Route('/{id}/join', name: 'app_groupe_join', methods: ['POST'])]
     public function join(Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
         if ($groupe->isFull()) {
             $this->addFlash('warning', 'Ce groupe est complet, impossible d\'ajouter un nouveau membre.');
             return $this->redirectToRoute('app_groupe_index', ['id' => $groupe->getId()]);
         }
    
         
     }
}