<?php

namespace App\Repository;

use App\Entity\CalendarEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CalendarEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalendarEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalendarEvent[]    findAll()
 * @method CalendarEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarEvent::class);
    }

    /**
     * @param Int $userID
     * @return CalendarEvent[] Returns an array of CalendarEvent objects
     */
    public function findByUserID(Int $userID)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
            ->setParameter('val', $userID)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Int $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByID(Int $id): ?CalendarEvent
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * return calendar event by user id
     *
     * @param int $userid
     * @return CalendarEvent
     */
    public function findCalendarByUserId($userid)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :val')
            ->setParameter('val', $userid)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?CalendarEvent
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
