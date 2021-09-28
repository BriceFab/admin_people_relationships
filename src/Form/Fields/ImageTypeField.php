<?php

namespace App\Form\Fields;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class ImageTypeField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string $propertyName
     * @param null $label
     * @return ImageTypeField
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/image')
            ->setFormType(FileUploadType::class)
            ->addCssClass('field-image')
            ->setTemplatePath('admin/fields/image.html.twig')
            ->setTextAlign(TextAlign::CENTER);
    }

}
