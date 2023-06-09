<?php
/**
 * https://github.com/andrew72ru/symfony-db-i18n-bundle
 */

declare(strict_types=1);

namespace App\Classes\Interfaces;

/**
 * Interface EntityInterface.
 *
 * @package App\Interfaces
 *
 * Database entity for store translation MUST implements this interface
 */
interface EntityInterface
{
    /**
     * Load translated string.
     *
     * @return string|null
     */
    public function getTranslation(): ?string;

    /**
     * Load data to entity.
     * For example: imagine that entity has `domain`, `locale`, `key` and `translation` params
     * This method may be called as
     * ```
     * $entity->load([
     *    'domain' => $domain,
     *    'locale' => $locale,
     *    'key' => $key,
     *    'translation' => $translation,
     * ]);
     * ```
     * and return valid entity for store in database.
     *
     * @param array $params
     *
     * @return EntityInterface
     */
    public function load(array $params): self;
}
