<?php

namespace App\Repository;

use App\Entity\Chatroom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    // /**
    //  * @return Chatroom[] Returns an array of Chatroom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
