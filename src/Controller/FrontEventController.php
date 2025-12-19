<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\EvenementEcologique;
use App\Entity\Participation;
use App\Entity\StatutParticipation;
use App\Entity\GroupeRequest;
use App\Form\AvisType;
use App\Repository\EvenementEcologiqueRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/events', name: 'app_front_events')]
class FrontEventController extends AbstractController
{
    #[Route('', name: '')]
    public function index(
        EvenementEcologiqueRepository $eventRepo,
        Request $request
    ): Response {
        $category = $request->query->get('category');
        $search = $request->query->get('search');
        $titre = $request->query->get('titre');
        $lieu = $request->query->get('lieu');
        $categorie = $request->query->get('categorie');
        $disponible = $request->query->get('disponible');
        
        $queryBuilder = $eventRepo->createQueryBuilder('e')
            ->where('e.dateFin >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateDebut', 'ASC');

        if ($search || $titre) {
            $searchTerm = $search ?: $titre;
            $queryBuilder->andWhere('e.titre LIKE :search OR e.description LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%');
        }

        if ($lieu) {
            $queryBuilder->andWhere('e.lieu LIKE :lieu')
                ->setParameter('lieu', '%' . $lieu . '%');
        }

        if ($category || $categorie) {
            $cat = $category ?: $categorie;
            $queryBuilder->andWhere('JSON_CONTAINS(e.categorie, :category) = 1')
                ->setParameter('category', json_encode($cat));
        }

        if ($disponible === 'oui') {
            $queryBuilder->andWhere('e.nombreParticipants < e.capaciteMax');
        } elseif ($disponible === 'non') {
            $queryBuilder->andWhere('e.nombreParticipants >= e.capaciteMax');
        }

        $evenements = $queryBuilder->getQuery()->getResult();

        $filters = [
            'titre' => $titre ?? '',
            'lieu' => $lieu ?? '',
            'categorie' => $categorie ?? '',
            'disponible' => $disponible ?? '',
        ];

        return $this->render('front/events/index.html.twig', [
            'evenements' => $evenements,
            'currentCategory' => $category,
            'currentSearch' => $search,
            'filters' => $filters,
        ]);
    }

    #[Route('/{id}', name: '_show', requirements: ['id' => '\d+'])]
    public function show(
        EvenementEcologique $event,
        ParticipationRepository $participationRepo
    ): Response {
        $user = $this->getUser();
        $participation = null;
        $canReview = false;

        if ($user) {
            $participation = $participationRepo->findOneBy([
                'idEvenement' => $event,
                'idUser' => $user
            ]);

            // User can review if they participated and event has ended
            $canReview = $participation && $event->getDateFin() < new \DateTime();
        }

        $participantsCount = count($event->getParticipations());
        $availableSpots = $event->getCapaciteMax() - $participantsCount;

        return $this->render('front/events/show.html.twig', [
            'event' => $event,
            'participation' => $participation,
            'participantsCount' => $participantsCount,
            'availableSpots' => $availableSpots,
            'canReview' => $canReview,
        ]);
    }

    #[Route('/{id}/register', name: '_register', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function register(
        EvenementEcologique $event,
        EntityManagerInterface $em,
        ParticipationRepository $participationRepo
    ): Response {
        $user = $this->getUser();

        // Check if already registered
        $existingParticipation = $participationRepo->findOneBy([
            'idEvenement' => $event,
            'idUser' => $user
        ]);

        if ($existingParticipation) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        // Check capacity
        $participantsCount = count($event->getParticipations());
        if ($participantsCount >= $event->getCapaciteMax()) {
            $this->addFlash('danger', 'Désolé, cet événement est complet.');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        // Check if event is in the future
        if ($event->getDateDebut() < new \DateTime()) {
            $this->addFlash('danger', 'Cet événement a déjà commencé.');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        // Create participation
        $participation = new Participation();
        $participation->setIdUser($user);
        $participation->setIdEvenement($event);
        $participation->setDateInscription(new \DateTime());
        $participation->setStatut(StatutParticipation::INSCRIT);

        $em->persist($participation);
        
        // Create a group assignment request for admin/organizer approval
        $groupeRequest = new GroupeRequest();
        $groupeRequest->setUser($user);
        $groupeRequest->setEvenement($event);
        $groupeRequest->setStatut('PENDING');
        
        $em->persist($groupeRequest);
        $em->flush();

        $this->addFlash('success', 'Votre inscription a été confirmée ! Un administrateur vous assignera à un groupe prochainement.');
        return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
    }

    #[Route('/{id}/cancel', name: '_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(
        EvenementEcologique $event,
        EntityManagerInterface $em,
        ParticipationRepository $participationRepo
    ): Response {
        $user = $this->getUser();

        $participation = $participationRepo->findOneBy([
            'idEvenement' => $event,
            'idUser' => $user
        ]);

        if (!$participation) {
            $this->addFlash('danger', 'Vous n\'êtes pas inscrit à cet événement.');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        // Don't allow cancellation for past events
        if ($event->getDateDebut() < new \DateTime()) {
            $this->addFlash('danger', 'Impossible d\'annuler une participation à un événement passé.');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        $participation->setStatut(StatutParticipation::ANNULE);
        $em->flush();

        $this->addFlash('info', 'Votre participation a été annulée.');
        return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
    }

    #[Route('/{id}/review', name: '_review', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function review(
        Request $request,
        EvenementEcologique $event,
        EntityManagerInterface $em,
        ParticipationRepository $participationRepo
    ): Response {
        $user = $this->getUser();

        // Check if user participated
        $participation = $participationRepo->findOneBy([
            'idEvenement' => $event,
            'idUser' => $user
        ]);

        if (!$participation || $event->getDateFin() >= new \DateTime()) {
            $this->addFlash('danger', 'Vous ne pouvez laisser un avis que pour les événements auxquels vous avez participé et qui sont terminés.');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        // Check if already reviewed
        $existingAvis = $em->getRepository(Avis::class)->findOneBy([
            'idEvenement' => $event,
            'idUser' => $user
        ]);

        if ($existingAvis) {
            $this->addFlash('warning', 'Vous avez déjà laissé un avis pour cet événement.');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setIdUser($user);
            $avis->setIdEvenement($event);
            $avis->setDateAvis(new \DateTime());

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre avis !');
            return $this->redirectToRoute('app_front_events_show', ['id' => $event->getId()]);
        }

        return $this->render('front/events/review.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }
}
