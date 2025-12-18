<?php

namespace App\Repository;

use App\Entity\ZoneCollecte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ZoneCollecteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZoneCollecte::class);
    }

    /**
     * Recherche des zones par nom, adresse ou type de déchet
     */
   public function searchZones(string $search): array
{
    return $this->createQueryBuilder('z')
        ->where('z.nom LIKE :search')
        ->orWhere('z.adresse LIKE :search')
        ->setParameter('search', '%' . $search . '%')
        ->getQuery()
        ->getResult();
}
}