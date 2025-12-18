<?php

namespace App\Controller;

use App\Repository\MembreGroupeRepository;
use App\Repository\GroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard')]
    public function index(
        MembreGroupeRepository $membreGroupeRepo,
        GroupeRepository $groupeRepo
    ): Response {
        
        $membersPerGroup = $this->getMembersCountByGroup($membreGroupeRepo);
        
        
        $statusDistribution = $this->getStatusDistribution($membreGroupeRepo);
        
       
        $groupsStats = $this->getGroupsStats($groupeRepo);
        
         
        $membersPerMonth = $this->getMembersPerMonth($membreGroupeRepo);

        return $this->render('dashboard/index.html.twig', [
            'membersPerGroup' => json_encode($membersPerGroup),
            'statusDistribution' => json_encode($statusDistribution),
            'groupsStats' => $groupsStats,
            'membersPerMonth' => json_encode($membersPerMonth),
        ]);
    }

    private function getMembersCountByGroup(MembreGroupeRepository $repo): array
    {
        $data = [['Groupe', 'Membres']];
        
        $result = $repo->createQueryBuilder('m')
            ->select('g.nom, COUNT(m.id) as count')
            ->join('m.idGroupe', 'g')
            ->groupBy('g.id')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
        
        foreach ($result as $row) {
            $data[] = [$row['nom'], (int)$row['count']];
        }
        
        return $data;
    }

    private function getStatusDistribution(MembreGroupeRepository $repo): array
    {
        $data = [['Statut', 'Nombre']];
        
        $result = $repo->createQueryBuilder('m')
            ->select('m.statut, COUNT(m.id) as count')
            ->groupBy('m.statut')
            ->getQuery()
            ->getResult();
        
        foreach ($result as $row) {
            $statusLabel = $row['statut']->label();
            $data[] = [$statusLabel, (int)$row['count']];
        }
        
        return $data;
    }

    private function getGroupsStats(GroupeRepository $repo): array
    {
        $groupes = $repo->findAll();
        $stats = [];
        
        foreach ($groupes as $groupe) {
            $stats[] = [
                'nom' => $groupe->getNom(),
                'description' => $groupe->getDescription(),
                'nombreMembres' => $groupe->getNombreMembres(),
                'activeMembersCount' => $groupe->getActiveMembersCount(),
                'disponible' => $groupe->getNombreMembres() - $groupe->getActiveMembersCount(),
            ];
        }
        
        return $stats;
    }

    private function getMembersPerMonth(MembreGroupeRepository $repo): array
    {
        $data = [['Mois', 'Adhésions']];
        
        
        $allMembers = $repo->findAll();
        $monthCounts = [];
        
        foreach ($allMembers as $member) {
            if ($member->getDateAdhesion()) {
                $month = $member->getDateAdhesion()->format('Y-m');
                $monthCounts[$month] = ($monthCounts[$month] ?? 0) + 1;
            }
        }
        
      
        ksort($monthCounts);
        
       
        foreach ($monthCounts as $month => $count) {
            $data[] = [$month, $count];
        }
        
        return $data;
    }
}
