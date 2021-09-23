<?php

namespace App\Repository;

use App\Classes\Interfaces\TranslationRepositoryInterface;
use App\Entity\Traduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Traduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Traduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Traduction[]    findAll()
 * @method Traduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TraductionRepository extends ServiceEntityRepository implements TranslationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traduction::class);
    }

    /**
     * @param string $domain
     * @param string $locale
     *
     * @return int|mixed|string
     */
    public function findByDomainAndLocale(string $domain, string $locale)
    {
        return $this->createQueryBuilder('t', 't.key')
            ->where('t.domain = :domain')
            ->andWhere('t.locale = :locale')
            ->setParameter('domain', $domain)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $locale
     *
     * @return ArrayCollection
     */
    public function findForUpdate(string $locale): ArrayCollection
    {
        $result = $this->createQueryBuilder('t')
            ->where('t.locale = :locale')
            ->setParameter('locale', $locale)
            ->orderBy('t.key', 'ASC')
            ->getQuery()->getResult();

        return new ArrayCollection($result);
    }

    public function listLocales(): array
    {
        $qb = $this->createQueryBuilder("t");

        $qb
            ->select("DISTINCT(t.locale)");

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        if (count($result) > 0) {
            $locales = [];
            foreach ($result as $item) {
                $locale = $item[array_key_first($item)] ?? null;
                if (!is_null($locale)) {
                    $locales[] = $locale;
                }
            }
            return $locales;
        }

        return [];
    }

}
