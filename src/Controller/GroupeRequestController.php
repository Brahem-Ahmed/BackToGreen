<?php

namespace App\Controller;

use App\Entity\GroupeRequest;
use App\Entity\MembreGroupe;
use App\Repository\GroupeRequestRepository;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/groupe-requests')]
final class GroupeRequestController extends AbstractController
{
    #[Route('', name: 'app_groupe_requests', methods: ['GET'])]
    public function index(
        GroupeRequestRepository $groupeRequestRepository,
        GroupeRepository $groupeRepository
    ): Response {
        $pendingRequests = $groupeRequestRepository->findPendingRequests();
        $allGroupes = $groupeRepository->findAll();

        return $this->render('groupe_request/index.html.twig', [
            'pending_requests' => $pendingRequests,
            'all_groupes' => $allGroupes,
        ]);
    }

    #[Route('/{id}/assign', name: 'app_groupe_request_assign', methods: ['POST'])]
    public function assign(
        GroupeRequest $groupeRequest,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $groupeId = $request->request->get('groupe_id');
        
        if (!$groupeId) {
            $this->addFlash('error', 'Please select a group.');
            return $this->redirectToRoute('app_groupe_requests');
        }

        $groupe = $entityManager->getRepository(\App\Entity\Groupe::class)->find($groupeId);
        
        if (!$groupe) {
            $this->addFlash('error', 'Group not found.');
            return $this->redirectToRoute('app_groupe_requests');
        }

        // Update the group request
        $groupeRequest->setStatut('APPROVED');
        $groupeRequest->setAssignedGroupe($groupe);
        $groupeRequest->setProcessedAt(new \DateTime());
        $groupeRequest->setProcessedBy($this->getUser());

        // Create MembreGroupe entry
        $membreGroupe = new MembreGroupe();
        $membreGroupe->setUser($groupeRequest->getUser());
        $membreGroupe->setGroupe($groupe);
        $membreGroupe->setDateAjout(new \DateTime());
        $membreGroupe->setStatut('active');

        $entityManager->persist($membreGroupe);
        $entityManager->flush();

        $this->addFlash('success', sprintf(
            'User %s has been assigned to group %s.',
            $groupeRequest->getUser()->getUsername(),
            $groupe->getNom()
        ));

        return $this->redirectToRoute('app_groupe_requests');
    }

    #[Route('/{id}/reject', name: 'app_groupe_request_reject', methods: ['POST'])]
    public function reject(
        GroupeRequest $groupeRequest,
        EntityManagerInterface $entityManager
    ): Response {
        $groupeRequest->setStatut('REJECTED');
        $groupeRequest->setProcessedAt(new \DateTime());
        $groupeRequest->setProcessedBy($this->getUser());

        $entityManager->flush();

        $this->addFlash('warning', sprintf(
            'Group request from %s has been rejected.',
            $groupeRequest->getUser()->getUsername()
        ));

        return $this->redirectToRoute('app_groupe_requests');
    }
}
