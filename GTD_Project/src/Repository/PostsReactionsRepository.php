<?php

namespace App\Repository;

use App\Entity\PostsReactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostsReactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostsReactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostsReactions[]    findAll()
 * @method PostsReactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsReactionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostsReactions::class);
    }

    public function checkPoceBleu($userId, $postId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.post = :postId')
            ->andWhere('p.user = :userId')
            ->setParameter('postId', $postId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return PostsReactions[] Returns an array of PostsReactions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PostsReactions
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
