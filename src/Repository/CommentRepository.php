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
     * @param Trick $trick
     * @param int $page
     * @param int $limit
     * @return Comment[] Returns an array of Comment objects
     */
    public function findPaginatedByTrick(Trick $trick, int $page, int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            // This is the missing part: we create the query and get the results
            ->getQuery()
            ->getResult();
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
