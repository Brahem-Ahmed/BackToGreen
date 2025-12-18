<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avisClient')]
final class AvisClientController extends AbstractController
{
    private static ?User $globalUser = null;


    #[Route('/client/{id}', name: 'app_user_avis')]
public function avisClient(User $User, AvisRepository $avisRepository): Response
{
    self::$globalUser = $User;
    // Récupérer tous les avis du client spécifié
    $avis = $avisRepository->findBy(
        ['idUser' => $User],
        ['dateAvis' => 'DESC'] // Optionnel : tri par date décroissante
    );

    return $this->render('avis/affichec.html.twig', [
        'User' => $User,
        'avis' => $avis,
    ]);
}

                                                                                                                                                                                                                              

   #[Route('/new/{userId}', name: 'app_avis_new_for_user', methods: ['GET', 'POST'])]
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
        throw $this->createNotFoundException('User not found with id: ' . $userId);
    }
    
    // 3. Vérifier les permissions (optionnel)
    $currentUser = $this->getUser();
    if ($currentUser && $currentUser->getId() !== $user->getId()) {
        // Seulement si vous voulez limiter aux propres avis
        // throw $this->createAccessDeniedException('You can only create reviews for yourself');
    }
    
    // 4. Créer le nouvel avis
    $avi = new Avis();
    $avi->setDateAvis(new \DateTime());
    $avi->setIdUser($user);
    // Associer l'utilisateur (vérifiez le nom de la méthode)
    if (method_exists($avi, 'setUser')) {
        $avi->setUser($user);
    } elseif (method_exists($avi, 'setIdUser')) {
        $avi->setIdUser($user);
    } else {
        throw new \Exception('No method to set user on Avis entity');
    }
    
    // 5. Gérer le formulaire
    $form = $this->createForm(AvisType::class, $avi);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($avi);
        $entityManager->flush();

        // 6. Rediriger
        $this->addFlash('success', 'Avis créé avec succès!');
        return $this->redirectToRoute('app_client_avis', [
            'id' => $user->getId()
        ], Response::HTTP_SEE_OTHER);
    }

    // 7. Afficher le formulaire
    return $this->render('avis/new.html.twig', [
        'avi' => $avi,
        'form' => $form,
        'user' => $user,  // Important pour le template
    ]);
}
#[Route('/{id}/edit', name: 'app_avis_edittt', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
    
    // 3. Vérifier les permissions (optionnel)
    $currentUser = $this->getUser();
    $aviUser = null;
    
    if (method_exists($avi, 'getUser')) {
        $aviUser = $avi->getUser();
    } elseif (method_exists($avi, 'getIdUser')) {
        $aviUser = $avi->getIdUser();
    }
    
    
    
    // 4. Créer et gérer le formulaire
    $form = $this->createForm(AvisType::class, $avi);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Mettre à jour la date de modification
        $avi->setDateAvis(new \DateTime()); // ou ajoutez un champ dateModification
        
        $entityManager->flush();
        $this->addFlash('success', 'Avis modifié avec succès!');

        // 5. Redirection vers les avis de l'utilisateur
        if ($aviUser) {
            return $this->redirectToRoute('app_user_avis', [
                'id' => $aviUser->getId()
            ], Response::HTTP_SEE_OTHER);
        }

         return $this->render('avis/edit.html.twig', [
        'avi' => $avi,
        'form' => $form,
        'user' => $user, // ← PASSER L'UTILISATEUR AU TEMPLATE
    ]);
        
        
    }

    // 6. Afficher le formulaire d'édition
    return $this->render('avis/edit.html.twig', [
        'avi' => $avi,
        'form' => $form,
        'user' => $aviUser, // Passer l'utilisateur au template si besoin
    ]);
    }

    #[Route('/delete/{id}', name: 'app_avis_deletee', methods: ['POST'])]
    public function delete(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        
        
        
        // 3. Vérifier le token CSRF (CORRECTION ICI)
        if (!$this->isCsrfTokenValid('delete'.$avi->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_home');
        }
        
        // 4. Récupérer l'utilisateur AVANT suppression (IMPORTANT)
        $userId = null;
        
        if (method_exists($avi, 'getUser') && $avi->getUser()) {
            $userId = $avi->getUser()->getId();
        } elseif (method_exists($avi, 'getIdUser') && $avi->getIdUser()) {
            $userId = $avi->getIdUser()->getId();
        }
        
        // 5. Supprimer l'avis
        $entityManager->remove($avi);
        $entityManager->flush();
        $this->addFlash('success', 'Avis supprimé avec succès!');
        
        // 6. Redirection - SOLUTION DÉFINITIVE
        if ($userId) {
            // Rediriger vers les avis de l'utilisateur
            return $this->redirectToRoute('app_user_avis', [
                'id' => $userId
            ], Response::HTTP_SEE_OTHER);
    }

    


}
// Dans votre AvisController.php
    #[Route('/{id}', name: 'app_avis_showw', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
         $user = $avi->getIdUser();
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
            'user' => $user, 
        ]);
    }
}