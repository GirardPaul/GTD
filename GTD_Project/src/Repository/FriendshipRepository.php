<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function checkRelationExist($senderId, $targetId)
    {
        return $this->createQueryBuilder('f')
            ->select()
            ->andWhere('f.sender = :senderId OR f.sender = :targetId')
            ->andWhere('f.target = :targetId OR f.target = :senderId')
            ->setParameter('senderId', $senderId)
            ->setParameter('targetId', $targetId)
            ->andWhere('f.acceptedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function checkAskForFriend($targetId)
    {
        return $this->createQueryBuilder('f')
            ->select()
            ->andWhere('f.target = :targetId')
            ->setParameter('targetId', $targetId)
            ->andWhere('f.acceptedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function getAllRelationFriendsOfUser($userId)
    {
        return $this->createQueryBuilder('f')
            ->select()
            ->andWhere('f.sender = :userId')
            ->orWhere('f.target = :userId')
            ->setParameter('userId', $userId)
            ->andWhere('f.acceptedAt IS NOT NULL')
            ->orderBy('f.acceptedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function checkRelationFriendsExist($senderId, $targetId)
    {
        return $this->createQueryBuilder('f')
            ->select()
            ->andWhere('f.sender = :senderId OR f.sender = :targetId')
            ->andWhere('f.target = :targetId OR f.target = :senderId')
            ->setParameter('senderId', $senderId)
            ->setParameter('targetId', $targetId)
            ->andWhere('f.acceptedAt IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}
