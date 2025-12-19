<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\StatutReclamation;
use App\Form\ReclamationFrontType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reclamations')]
final class FrontReclamationController extends AbstractController
{
    #[Route('/new', name: 'app_front_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        // Ensure defaults are set before form binding
        $reclamation->setIdUser($this->getUser());
        
        $form = $this->createForm(ReclamationFrontType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Your complaint has been submitted successfully! We will review it and respond soon.');

            return $this->redirectToRoute('app_front_reclamation_list');
        }

        return $this->render('front/reclamation/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/my-complaints', name: 'app_front_reclamation_list', methods: ['GET'])]
    public function list(ReclamationRepository $reclamationRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Please log in to view your complaints.');
            return $this->redirectToRoute('app_login');
        }

        $reclamations = $reclamationRepository->findBy(
            ['idUser' => $user],
            ['dateReclamation' => 'DESC']
        );

        return $this->render('front/reclamation/list.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/{id}', name: 'app_front_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        // Check if user owns this reclamation
        if ($reclamation->getIdUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot view this complaint.');
        }

        return $this->render('front/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
}
