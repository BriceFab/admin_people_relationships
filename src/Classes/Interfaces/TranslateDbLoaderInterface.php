<?php
/**
 * https://github.com/andrew72ru/symfony-db-i18n-bundle
 */

declare(strict_types=1);

namespace App\Classes\Interfaces;

/**
 * Interface DbLoaderInterface.
 *
 * @package App\Interfaces
 */
interface TranslateDbLoaderInterface
{
    /**
     * @return TranslationRepositoryInterface
     */
    public function getRepository(): TranslationRepositoryInterface;
}
