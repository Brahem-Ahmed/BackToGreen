<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\User; 

use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reclamationClient')]
final class ReclamationClientController extends AbstractController
{
    #[Route('/client/{id}', name: 'app_user_reclamations', methods: ['GET'])]
    public function reclamationsClient(User $user, ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer toutes les réclamations du client spécifié
        $reclamations = $reclamationRepository->findBy(
            ['idUser' => $user->getId()], // Assurez-vous que la propriété s'appelle bien 'user' dans votre entité Reclamation
            ['dateReclamation' => 'DESC'] // Tri par date de création décroissante
        );

        return $this->render('reclamation/afficheC.html.twig', [
            'user' => $user,
            'reclamations' => $reclamations,
        ]);
    }

     // Route pour créer une réclamation pour un utilisateur spécifique
    #[Route('/new/{userId}', name: 'app_reclamation_new_for_user', methods: ['GET', 'POST'])]
    public function newForUser(
        Request $request, 
        EntityManagerInterface $entityManager, 
        int $userId  // Accepte un int
    ): Response
    {
        // 1. Récupérer l'utilisateur depuis la base de données
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);
        
        // 2. Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé avec l\'ID: ' . $userId);
        }
        
        // 3. Vérifier les permissions (optionnel)
        $currentUser = $this->getUser();
        if ($currentUser && $currentUser->getId() !== $user->getId()) {
            // Seulement si vous voulez limiter aux propres réclamations
            // throw $this->createAccessDeniedException('Vous ne pouvez créer des réclamations que pour vous-même');
            // Ou ajouter un message flash d'avertissement
            $this->addFlash('warning', 'Vous créez une réclamation pour un autre utilisateur.');
        }
        
        // 4. Créer la nouvelle réclamation
        $reclamation = new Reclamation();
        $reclamation->setDateReclamation(new \DateTime());
        
        // Associer l'utilisateur (vérifiez le nom de la méthode)
        if (method_exists($reclamation, 'setUser')) {
            $reclamation->setUser($user);
        } elseif (method_exists($reclamation, 'setIdUser')) {
            $reclamation->setIdUser($user);
        } else {
            // Essayez d'autres noms possibles
            if (method_exists($reclamation, 'setClient')) {
                $reclamation->setClient($user);
            } elseif (method_exists($reclamation, 'setUtilisateur')) {
                $reclamation->setUtilisateur($user);
            } else {
                throw new \Exception('Aucune méthode pour associer l\'utilisateur à l\'entité Reclamation');
            }
        }
        
        // 5. Gérer le formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // 6. Rediriger
            $this->addFlash('success', 'Réclamation créée avec succès!');
            return $this->redirectToRoute('app_user_reclamations', [
                'id' => $user->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        // 7. Afficher le formulaire
        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
            'user' => $user,  // Important pour le template
        ]);
    }


}