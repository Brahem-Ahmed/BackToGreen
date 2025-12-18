<?php

namespace App\Repository;

use App\Entity\PasswordReset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordReset>
 */
class PasswordResetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReset::class);
    }

    public function findValidByToken(string $token): ?PasswordReset
    {
        $reset = $this->findOneBy(['token' => $token]);

        if ($reset && $reset->isValid()) {
            return $reset;
        }

        return null;
    }

    public function deleteExpiredTokens(): int
    {
        return $this->createQueryBuilder('pr')
            ->delete()
            ->where('pr.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
