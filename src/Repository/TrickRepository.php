<?php

// src/Repository/TrickRepository.php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

/**
     * Finds a list of tricks with pagination.
     *
     * @param int $page  The current page number
     * @param int $limit The number of tricks to display per page
     *
     * @return array<Trick> Returns an array of Trick objects
     */
    public function findPaginated(int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        /** @var array<Trick> $results */
        $results = $qb->getQuery()->getResult();

        return $results;
    }

    /**
     * Counts the total number of Trick entities in the database.
     *
     * @return int the total count of Trick entities
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
