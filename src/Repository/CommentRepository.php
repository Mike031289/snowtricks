<?php

// src/Repository/CommentRepository.php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Finds a paginated list of comments for a specific trick.
     *
     * @param Trick $trick The trick entity associated with the comments
     * @param int $page The current page number
     * @param int $limit The maximum number of comments to return
     *
     * @return array<Comment> Returns a list of Comment objects
     */
    public function findPaginatedByTrick(Trick $trick, int $page, int $limit): array
    {
        // We build the query step by step
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        // We convert the builder into a real Query object
        $query = $queryBuilder->getQuery();

        // We execute and return the final array of entities
        /** @var array<Comment> $results */
        $results = $query->getResult();

        return $results;
    }

    /**
     * @return int Returns the count of comments for a trick
     */
    public function countByTrick(Trick $trick): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
