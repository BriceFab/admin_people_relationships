<?php

namespace App\Entity\Common;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;

trait TranslatableTrait
{
    use \Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

    public static function getTranslationEntityClass(): string
    {
        $explodedNamespace = explode('\\', __CLASS__);
        $entityClass = array_pop($explodedNamespace);

        return '\\' . implode('\\', $explodedNamespace) . '\\Translation\\' . $entityClass . 'Translation';
    }

    /**
     * @Assert\Valid
     */
    protected $translations;

    public function __call($method, $arguments)
    {
        return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $method);
    }

    /**
     * Retourne l'entité de traduction depuis la locale
     * @return false|mixed|null
     */
    public function getLocaleTranslate()
    {
        $defaultLocale = $this->getCurrentLocale();
        $translations = $this->getTranslations();
        $translation = array_filter($translations->toArray(), function ($translation) use ($defaultLocale) {
            /** @var TranslationTrait $translation */
            return $translation->getLocale() === $defaultLocale;
        });
        if (count($translation) > 0) {
            return current($translation);
        }
        return null;
    }

}