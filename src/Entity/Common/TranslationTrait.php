<?php

namespace App\Entity\Common;

trait TranslationTrait
{
    use \Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

    public static function getTranslatableEntityClass(): string
    {
        $explodedNamespace = explode('\\', __CLASS__);
        $entityClass = array_pop($explodedNamespace);
        // Remove Translation namespace
        array_pop($explodedNamespace);

        return '\\' . implode('\\', $explodedNamespace) . '\\' . substr($entityClass, 0, -11);
    }
}