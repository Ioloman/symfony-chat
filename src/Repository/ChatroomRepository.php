<?php

namespace App\Repository;

use App\Entity\Chatroom;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Chatroom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chatroom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chatroom[]    findAll()
 * @method Chatroom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatroomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chatroom::class);
    }


     /**
      * @return Chatroom[] Returns an array of Chatroom objects
      */
    public function findChatroomsByUser(?UserInterface $user)
    {
        return
            $user
            ?
            $this->createQueryBuilder('c')
            ->innerJoin('c.users', 'u')
            ->andWhere('u.id = :user_id')
            ->setParameter('user_id', $user->getId())
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            :
            null
        ;
    }

    public function findChatroomsByQuery(string $query)
    {
        return $this
            ->createQueryBuilder('c')
            ->andWhere('c.type = public')
            ->andWhere()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Chatroom
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
