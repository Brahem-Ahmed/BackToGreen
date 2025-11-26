<?php

namespace App\Controller;

use App\Entity\EvenementEcologique;
use App\Form\EvenementEcologiqueType;
use App\Repository\EvenementEcologiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenementecologique')]
final class EvenementEcologiqueController extends AbstractController
{
    #[Route(name: 'app_evenement_ecologique_index', methods: ['GET'])]
    public function index(EvenementEcologiqueRepository $evenementEcologiqueRepository): Response
    {
        return $this->render('evenement_ecologique/index.html.twig', [
            'evenement_ecologiques' => $evenementEcologiqueRepository->findAll(),
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
}
