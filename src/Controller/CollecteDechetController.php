<?php

namespace App\Controller;

use App\Entity\CollecteDechet;
use App\Form\CollecteDechetType;
use App\Repository\CollecteDechetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/collecte/dechet')]
final class CollecteDechetController extends AbstractController
{
    #[Route(name: 'app_collecte_dechet_index', methods: ['GET'])]
    public function index(CollecteDechetRepository $collecteDechetRepository): Response
    {
        return $this->render('collecte_dechet/index.html.twig', [
            'collecte_dechets' => $collecteDechetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_collecte_dechet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collecteDechet = new CollecteDechet();
        $form = $this->createForm(CollecteDechetType::class, $collecteDechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collecteDechet);
            $entityManager->flush();

            return $this->redirectToRoute('app_collecte_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collecte_dechet/new.html.twig', [
            'collecte_dechet' => $collecteDechet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collecte_dechet_show', methods: ['GET'])]
    public function show(CollecteDechet $collecteDechet): Response
    {
        return $this->render('collecte_dechet/show.html.twig', [
            'collecte_dechet' => $collecteDechet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collecte_dechet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CollecteDechet $collecteDechet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollecteDechetType::class, $collecteDechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_collecte_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collecte_dechet/edit.html.twig', [
            'collecte_dechet' => $collecteDechet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collecte_dechet_delete', methods: ['POST'])]
    public function delete(Request $request, CollecteDechet $collecteDechet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collecteDechet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($collecteDechet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_collecte_dechet_index', [], Response::HTTP_SEE_OTHER);
    }
}
