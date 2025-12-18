<?php

namespace App\Controller;

use App\Entity\MembreGroupe;
use App\Entity\StatutMembre;
use App\Form\MembreGroupeType;
use App\Repository\MembreGroupeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/membre/groupe')]
final class MembreGroupeController extends AbstractController
{
    #[Route('', name: 'app_membre_groupe_index', methods: ['GET'])]
    public function index(MembreGroupeRepository $membreGroupeRepository): Response
    {
        return $this->render('membre_groupe/index.html.twig', [
            'membre_groupes' => $membreGroupeRepository->findAllValid(),
        ]);
    }

    #[Route('/search', name: 'app_membre_groupe_search', methods: ['GET'])]
    public function search(Request $request, MembreGroupeRepository $membreGroupeRepository): Response
    {
        $status = $request->query->get('status', '');

        $qb = $membreGroupeRepository->createQueryBuilder('mg')
            ->leftJoin('mg.idUser', 'u')
            ->leftJoin('mg.idGroupe', 'g')
            ->addSelect('PARTIAL u.{id, email, prenom, nom}')
            ->addSelect('PARTIAL g.{id, nom, nombreMembres}')
            ->orderBy('mg.dateAdhesion', 'DESC');

        if ($status !== '') {
            $qb->andWhere('mg.statut = :status')->setParameter('status', $status);
        } else {
            $qb->andWhere('mg.statut != :pending')->setParameter('pending', 'EN_ATTENTE');
        }

        $membres = $qb->getQuery()->getResult();

        return $this->render('membre_groupe/_members_rows.html.twig', [
            'membre_groupes' => $membres,
        ]);
    }

    #[Route('/new', name: 'app_membre_groupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $membreGroupe = new MembreGroupe();
        $form = $this->createForm(MembreGroupeType::class, $membreGroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $membreGroupe->setDateAdhesion(new \DateTime());

            
            $dejaMembre = $entityManager->getRepository(MembreGroupe::class)->findOneBy([
                'idUser'   => $membreGroupe->getIdUser(),
                'idGroupe' => $membreGroupe->getIdGroupe(),
            ]);

            if ($dejaMembre) {
                $this->addFlash('warning', 'Cet utilisateur est déjà membre de ce groupe.');
            } else {
                $entityManager->persist($membreGroupe);
                $entityManager->flush();

                $this->addFlash('success', 'Membre ajouté avec succès !');
                return $this->redirectToRoute('app_membre_groupe_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('membre_groupe/new.html.twig', [
            'membre_groupe' => $membreGroupe,
            'form'          => $form->createView(),
        ]);
    }

    /*#[Route('/all-members', name: 'app_all_members', methods: ['GET'])]
    public function allMembers(UserRepository $userRepository): Response
    {
        return $this->render('membre_groupe/all_members.html.twig', [
            'users' => $userRepository->findAllMembersWithMemberships(),
        ]);
    }*/

   /* #[Route('/pending', name: 'app_membre_groupe_pending', methods: ['GET'])]
    #[IsGranted('ROLE_MODERATEUR')]
    public function pending(MembreGroupeRepository $membreGroupeRepository): Response
    {
        $pendingRequests = $membreGroupeRepository->findPending();

        return $this->render('membre_groupe/pending.html.twig', [
            'pending_requests' => $pendingRequests,
        ]);
    }*/

    #[Route('/{id}', name: 'app_membre_groupe_show', methods: ['GET'])]
    public function show(MembreGroupe $membreGroupe): Response
    {
        return $this->render('membre_groupe/show.html.twig', [
            'membre_groupe' => $membreGroupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_membre_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MembreGroupe $membreGroupe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MembreGroupeType::class, $membreGroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Membre modifié avec succès !');
            return $this->redirectToRoute('app_membre_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('membre_groupe/edit.html.twig', [
            'membre_groupe' => $membreGroupe,
            'form'          => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_membre_groupe_delete', methods: ['POST'])]
    public function delete(Request $request, MembreGroupe $membreGroupe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $membreGroupe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($membreGroupe);
            $entityManager->flush();
            $this->addFlash('success', 'Membre supprimé avec succès.');
        }

        return $this->redirectToRoute('app_membre_groupe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/approve', name: 'app_membre_groupe_approve', methods: ['POST'])]
    #[IsGranted('ROLE_MODERATEUR')]
    public function approve(Request $request, ?MembreGroupe $membreGroupe, EntityManagerInterface $entityManager): Response
    {
        if (!$membreGroupe) {
            $this->addFlash('error', 'Demande introuvable.');
            return $this->redirectToRoute('app_membre_groupe_pending');
        }

        if (!$this->isCsrfTokenValid('approve' . $membreGroupe->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_membre_groupe_pending');
        }

        if ($membreGroupe->getStatut()->value === 'EN_ATTENTE') {
            $membreGroupe->setStatut(StatutMembre::MEMBRE_ACTIF);
            $membreGroupe->setDateAdhesion(new \DateTimeImmutable());
            $entityManager->flush();
            $this->addFlash('success', 'Membre approuvé !');
        }

        return $this->redirectToRoute('app_membre_groupe_pending');
    }

    #[Route('/{id}/reject', name: 'app_membre_groupe_reject', methods: ['POST'])]
    #[IsGranted('ROLE_MODERATEUR')]
    public function reject(Request $request, ?MembreGroupe $membreGroupe, EntityManagerInterface $entityManager): Response
    {
        if (!$membreGroupe) {
            $this->addFlash('error', 'Demande introuvable.');
            return $this->redirectToRoute('app_membre_groupe_pending');
        }

        if (!$this->isCsrfTokenValid('reject' . $membreGroupe->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_membre_groupe_pending');
        }

        if ($membreGroupe->getStatut()->value === 'EN_ATTENTE') {
            $entityManager->remove($membreGroupe);
            $entityManager->flush();
            $this->addFlash('info', 'Demande rejetée et supprimée.');
        }

        return $this->redirectToRoute('app_membre_groupe_pending');
    }
}