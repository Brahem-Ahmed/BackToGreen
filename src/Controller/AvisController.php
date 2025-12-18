<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avis')]
final class AvisController extends AbstractController
{
    #[Route(name: 'app_avis_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, AvisRepository $avisRepository): Response
    {
        $qb = $avisRepository->createQueryBuilder('a')
            ->orderBy('a.dateAvis', 'DESC');

        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $pagination = $paginator->paginate($qb, $page, $limit);

        return $this->render('avis/index.html.twig', [
            'avis' => $pagination,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/client/{id}', name: 'app_client_avis')]
public function avisClient(User $User, AvisRepository $avisRepository): Response
{
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

   // AvisController.php - Correction de la méthode search()
#[Route('/search', name: 'app_avis_search', methods: ['GET'])]
public function search(Request $request, PaginatorInterface $paginator, AvisRepository $avisRepository): Response
{
    $search = $request->query->get('search', '');
    $note = $request->query->get('note', '');
    
    $qb = $avisRepository->createQueryBuilder('a');
    
    if ($search) {
        $qb->andWhere('a.commentaire LIKE :search')
           ->setParameter('search', '%' . $search . '%');
    }
    
    if ($note && $note !== 'all') {
        $qb->andWhere('a.note = :note')
           ->setParameter('note', (int) $note);
    }
    
    $page = $request->query->getInt('page', 1);
    $limit = 10;

    $pagination = $paginator->paginate($qb, $page, $limit);

    return $this->render('avis/liste.html.twig', [
        'avis' => $pagination,
        'pagination' => $pagination,
    ]);
}                                                                                                                                                                                                                                                   

  /*  #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avi->setDateAvis(new \DateTime()); // Ajout automatique de la date
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }*/

    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
        return $this->render('avis/showadmin.html.twig', [
            'avi' => $avi,
        ]);
    }

   /* #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }*/

    #[Route('/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
    }

    // Dans votre AvisController.php


}
