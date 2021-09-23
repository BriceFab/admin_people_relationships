<?php

namespace App\Repository;

use App\Entity\TelephoneType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TelephoneType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelephoneType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelephoneType[]    findAll()
 * @method TelephoneType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelephoneTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelephoneType::class);
    }

    // /**
    //  * @return TelephoneType[] Returns an array of TelephoneType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TelephoneType
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
