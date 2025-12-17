<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find all users with their group memberships (whether assigned or not)
     * @return User[] Returns all users ordered by email
     */
    public function findAllWithMemberships(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.membreGroupes', 'm')
            ->addSelect('m')
            ->leftJoin('m.idGroupe', 'g')
            ->addSelect('g')
            ->orderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all ROLE_MEMBRE users with their group memberships
     * @return User[] Returns all members ordered by email
     */
    public function findAllMembersWithMemberships(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere("u.role = 'ROLE_MEMBRE'")
            ->leftJoin('u.membreGroupes', 'm')
            ->addSelect('m')
            ->leftJoin('m.idGroupe', 'g')
            ->addSelect('g')
            ->orderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
