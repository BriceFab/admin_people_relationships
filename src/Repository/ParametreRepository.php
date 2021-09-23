<?php

namespace App\Repository;

use App\Entity\Parametre;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parametre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parametre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parametre[]    findAll()
 * @method Parametre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parametre::class);
    }

    /**
     * Retourne la valeur du param
     * @param $cle
     * @param bool $retourneValeur
     * @return string|null|Parametre
     */
    public function findActiveParam($cle, bool $retourneValeur = true)
    {
        $maintenant = new DateTime();
        $maintenant->setTime(0, 0);

        try {
            /** @var Parametre $param */
            $param = $this->createQueryBuilder('p')
                ->andWhere('p.cle = :cle')
                ->andWhere('p.dateFinValidite is null OR p.dateFinValidite >= :maintenant')
                ->orderBy('p.dateFinValidite', 'DESC')
                ->setMaxResults(1)
                ->setParameter('cle', $cle)
                ->setParameter('maintenant', $maintenant)
                ->getQuery()
                ->getOneOrNullResult();

            if ($retourneValeur) {
                if (isset($param)) {
                    return $param->getValeur();
                }
            } else {
                return $param;
            }

            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
