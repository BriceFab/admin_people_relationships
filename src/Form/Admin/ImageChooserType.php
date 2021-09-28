<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageChooserType extends AbstractType
{

    public function getBlockPrefix(): string
    {
        return 'd1f_image_chooser';
    }

    public function getParent(): string
    {
        return TextType::class;
    }

}