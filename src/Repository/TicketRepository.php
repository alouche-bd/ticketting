<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }


    public function findOnByEntity($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.entity IN (:val)')
            ->andWhere('t.status = :status')
            ->setParameter('status', 0)
            ->setParameter('val', $value)
            ->orderBy('t.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOn()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', 0)
            ->orderBy('t.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOff()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', 1)
            ->orderBy('t.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOffByEntity($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.entity IN (:val)')
            ->andWhere('t.status = :status')
            ->setParameter('status', 1)
            ->setParameter('val', $value)
            ->orderBy('t.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Ticket
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}