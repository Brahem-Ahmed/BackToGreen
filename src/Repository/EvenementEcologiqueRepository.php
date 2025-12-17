<?php

namespace App\Repository;

use App\Entity\EvenementEcologique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvenementEcologique>
 */
class EvenementEcologiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvenementEcologique::class);
    }

    /**
     * Search events by optional criteria: title, category, date range, lieu
     * @param array $criteria
     * @return EvenementEcologique[]
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.dateDebut', 'DESC');

        if (!empty($criteria['title'])) {
            $qb->andWhere('LOWER(e.titre) LIKE :title')
               ->setParameter('title', '%'.mb_strtolower($criteria['title']).'%');
        }

        if (!empty($criteria['categorie'])) {
            $qb->andWhere('LOWER(e.categorie) LIKE :cat')
               ->setParameter('cat', '%'.mb_strtolower($criteria['categorie']).'%');
        }

        if (!empty($criteria['lieu'])) {
            $qb->andWhere('LOWER(e.lieu) LIKE :lieu')
               ->setParameter('lieu', '%'.mb_strtolower($criteria['lieu']).'%');
        }

        if (!empty($criteria['date'])) {
            $date = new \DateTime($criteria['date']);
            $dateStart = (clone $date)->setTime(0, 0, 0);
            $dateEnd = (clone $date)->setTime(23, 59, 59);
            $qb->andWhere('(e.dateDebut BETWEEN :dateStart AND :dateEnd) OR (e.dateFin BETWEEN :dateStart AND :dateEnd)')
               ->setParameter('dateStart', $dateStart)
               ->setParameter('dateEnd', $dateEnd);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return EvenementEcologique[] Returns an array of EvenementEcologique objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EvenementEcologique
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
