<?php

namespace App\Form\Admin\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfilForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("nom", TextType::class, [
                "required" => false,
                "label" => "user.nom"
            ])
            ->add("prenom", TextType::class, [
                "required" => false,
                "label" => "user.prenom"
            ])
            ->add("email", EmailType::class, [
                "required" => true,
                "label" => "user.email"
            ])
            ->add("submit", SubmitType::class, [
                "label" => "action.update",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "class" => User::class
        ]);
    }

}
