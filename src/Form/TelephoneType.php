<?php

namespace App\Form;

use App\Entity\Telephone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TelephoneType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("type", EntityType::class, [
                "label" => "Type",
                "class" => \App\Entity\TelephoneType::class,
            ])
            ->add("numero", TextType::class, [
                "label" => "Numéro",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Telephone::class,
        ]);
    }

}