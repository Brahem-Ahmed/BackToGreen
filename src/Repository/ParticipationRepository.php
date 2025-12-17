<?php

namespace App\Repository;

use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participation>
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    /**
     * Search participations by several optional criteria.
     * Supported keys: 'user', 'event', 'statut', 'date'
     *
     * @param array $criteria
     * @return Participation[]
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.idUser', 'u')
            ->leftJoin('p.idEvenement', 'e')
            ->addSelect('u', 'e')
            ->orderBy('p.dateInscription', 'DESC');

        if (!empty($criteria['user'])) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(u.nom)', ':user'),
                $qb->expr()->like('LOWER(u.prenom)', ':user')
            ))
            ->setParameter('user', '%'.mb_strtolower($criteria['user']).'%');
        }

        if (!empty($criteria['event'])) {
            $qb->andWhere('LOWER(e.titre) LIKE :event')
               ->setParameter('event', '%'.mb_strtolower($criteria['event']).'%');
        }

        if (!empty($criteria['statut'])) {
            // allow passing enum or string
            $qb->andWhere('p.statut = :statut')
               ->setParameter('statut', $criteria['statut']);
        }

        if (!empty($criteria['date'])) {
            $date = new \DateTime($criteria['date']);
            $dateStart = (clone $date)->setTime(0, 0, 0);
            $dateEnd = (clone $date)->setTime(23, 59, 59);
            $qb->andWhere('p.dateInscription BETWEEN :dateStart AND :dateEnd')
               ->setParameter('dateStart', $dateStart)
               ->setParameter('dateEnd', $dateEnd);
        }

        return $qb->getQuery()->getResult();
    }
}
