<?php

namespace App\Repository;

use App\Entity\PersonneType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonneType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonneType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonneType[]    findAll()
 * @method PersonneType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonneType::class);
    }

    // /**
    //  * @return PersonneType[] Returns an array of PersonneType objects
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
    public function findOneBySomeField($value): ?PersonneType
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
