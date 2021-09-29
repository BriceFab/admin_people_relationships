<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("type", EntityType::class, [
                "label" => "Type",
                "class" => \App\Entity\AdresseType::class,
            ])
            ->add("adresse", TextType::class, [
                "label" => "Adresse",
            ])
            ->add("pays", CountryType::class, [
                "label" => "Pays",
                "preferred_choices" => ["CH"],
                "required" => true,
            ])
            ->add("npa", TextType::class, [
                "label" => "Npa",
            ])
            ->add("lieu", TextType::class, [
                "label" => "Lieu",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Adresse::class,
        ]);
    }

}