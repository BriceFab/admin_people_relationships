<?php

namespace App\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function get_class;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_class', [$this, 'get_class']),
            new TwigFunction('get_classname', [$this, 'get_classname']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('class_fields', [$this, 'class_fields']),
            new TwigFilter('entity_access_property', [$this, 'entity_access_property']),
        ];
    }

    public function getGlobals(): array
    {
        return [];
    }

    #region functions

    /**
     * Donne le nom de la classe complète de l'entité
     * @param $entity
     * @return string
     */
    public function get_class($entity): string
    {
        return get_class($entity);
    }

    /**
     * Donne le nom de la classe réel de l'entité
     * @param $class
     * @return string
     */
    public function get_classname($class): string
    {
        $exploded = explode("\\", $this->get_class($class));
        return array_pop($exploded);
    }
    #endregion functions

    #region filters
    /**
     * Permets de lister les champs d'une entité / class
     * @param $entity
     * @return int[]|string[]
     */
    public function class_fields($entity)
    {
        if (is_object($entity)) $entity = $this->get_class($entity);
        return $this->em->getClassMetadata($entity)->getFieldNames();
    }

    /**
     * Permet d'accéder à la propriété d'une entité "dynamiquement"
     * @param $entity
     * @param string $property
     * @return mixed|null
     */
    public function entity_access_property($entity, string $property)
    {
        $propertyAccessor = new PropertyAccessor();
        return $propertyAccessor->getValue($entity, $property);
    }
    #endregion filters

    #region globals
    #endregion globals
}
