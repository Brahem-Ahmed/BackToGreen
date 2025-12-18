<?php

namespace App\Controller;

use App\Entity\EvenementEcologique;
use App\Entity\MembreGroupe;
use App\Repository\EvenementEcologiqueRepository;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // CETTE LIGNE MANQUAIT !
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/', name: 'front_')]
class FrontController extends AbstractController
{
    #[Route('/evenements', name: 'evenement_list')]
    public function evenements(EvenementEcologiqueRepository $repo): Response
    {
        $evenements = $repo->findBy([], ['dateDebut' => 'DESC']);

        return $this->render('front/evenement/list.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenement/{id}/inscrire', name: 'evenement_inscrire')]
    #[IsGranted('ROLE_USER')]
    public function inscrireEvenement(
        EvenementEcologique $evenement,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $groupe = $evenement->getGroupe();

        if (!$groupe) {
            $this->addFlash('warning', 'Cet événement n\'est pas lié à un groupe.');
            return $this->redirectToRoute('front_evenement_list');
        }

        $dejaMembre = $em->getRepository(MembreGroupe::class)->findOneBy([
            'idUser' => $user,
            'idGroupe' => $groupe,
        ]);

        if ($dejaMembre) {
            $this->addFlash('info', 'Vous êtes déjà membre de ce groupe !');
        } else {
            $membre = new MembreGroupe();
            $membre->setIdUser($user);
            $membre->setIdGroupe($groupe);
            $membre->setStatut('MEMBRE_ACTIF');
            $membre->setDateAdhesion(new \DateTime());

            $em->persist($membre);
            $em->flush();

            $this->addFlash('success', 'Vous avez rejoint le groupe "' . $groupe->getNom() . '" !');
        }

        return $this->redirectToRoute('front_evenement_list');
    }
}