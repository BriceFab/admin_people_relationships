<?php

namespace App\Repository;

use App\Entity\ExceptionLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExceptionLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExceptionLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExceptionLog[]    findAll()
 * @method ExceptionLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExceptionLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExceptionLog::class);
    }
}
