<?php
/**
 * https://github.com/andrew72ru/symfony-db-i18n-bundle
 */

declare(strict_types=1);

namespace App\Classes\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectRepository;

interface TranslationRepositoryInterface extends ObjectRepository
{
    /**
     * @param string $domain
     * @param string $locale
     *
     * @return array|Collection|EntityInterface[]
     */
    public function findByDomainAndLocale(string $domain, string $locale);
}
