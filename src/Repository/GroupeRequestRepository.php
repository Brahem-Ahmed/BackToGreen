<?php

namespace App\Repository;

use App\Entity\GroupeRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupeRequest>
 */
class GroupeRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupeRequest::class);
    }

    public function findPendingRequests(): array
    {
        return $this->createQueryBuilder('gr')
            ->leftJoin('gr.user', 'u')
            ->leftJoin('gr.evenement', 'e')
            ->leftJoin('e.groupes', 'g')
            ->addSelect('u', 'e', 'g')
            ->where('gr.statut = :statut')
            ->setParameter('statut', 'PENDING')
            ->orderBy('gr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingByEvent($eventId): array
    {
        return $this->createQueryBuilder('gr')
            ->where('gr.statut = :statut')
            ->andWhere('gr.evenement = :event')
            ->setParameter('statut', 'PENDING')
            ->setParameter('event', $eventId)
            ->orderBy('gr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
