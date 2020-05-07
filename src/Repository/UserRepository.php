<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @throws \Doctrine\ORM\ORMException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param Int $id
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById(Int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
    * @param $resetToken
    * @return  User|null
    */
    public function findOneByToken($resetToken): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.resetToken = :val')
                ->setParameter('val', $resetToken)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return "Error Token not found";
        }
    }

//    /**
//     *
//     * @param array $criteria
//     * @param array|null $orderBy
//     * @return User|null
//     * @throws \Doctrine\ORM\NonUniqueResultException
//     */
//    public function findOneBy(array $criteria, array $orderBy = null) : ?User
//    {
//        foreach ($criteria as $key=>$value)
//        {
//            $criteriaKey = $key;
//            $val = $value;
//        }
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.:key = :val')
//            ->setParameter('key', $criteriaKey)
//            ->setParameter('val', $val)
//            ->getQuery()
//            ->getOneOrNullResult();
//    }

    /**
     * Used to return teacher by id
     *
     * @return User
     */
    public function findTeacher(int $id)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
