<?php

namespace App\Repository;

use App\Entity\MembreGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MembreGroupe>
 */
class MembreGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembreGroupe::class);
    }

    /**
     * Find all pending membership requests (status = EN_ATTENTE)
     * @return MembreGroupe[] Returns pending membership requests ordered by date
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.statut = :status')
            ->setParameter('status', 'EN_ATTENTE')
            ->orderBy('m.dateAdhesion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pending requests for a specific group
     * @return MembreGroupe[]
     
     */
   public function findAllValid(): array
{
    return $this->createQueryBuilder('mg')
        ->leftJoin('mg.idUser', 'u')
        ->leftJoin('mg.idGroupe', 'g')
        ->addSelect('PARTIAL u.{id, email, prenom, nom}')  // Charge seulement les champs nécessaires
        ->addSelect('PARTIAL g.{id, nom, nombreMembres}')
        ->andWhere('mg.statut != :pending')
        ->setParameter('pending', 'EN_ATTENTE')
        ->orderBy('mg.dateAdhesion', 'DESC')
        ->getQuery()
        ->getResult();
}
    public function findPendingByGroupe($idGroupe): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.statut = :status')
            ->andWhere('m.idGroupe = :groupe')
            ->setParameter('status', 'EN_ATTENTE')
            ->setParameter('groupe', $idGroupe)
            ->orderBy('m.dateAdhesion', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return MembreGroupe[] Returns an array of MembreGroupe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MembreGroupe
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
