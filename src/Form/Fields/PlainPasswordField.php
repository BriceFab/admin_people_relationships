<?php

namespace App\Form\Fields;

use App\Config\ConstraintsConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlainPasswordField extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => PasswordType::class,
            'first_options' => [
                'constraints' => ConstraintsConfig::getPasswordConstraints(),
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label' => 'password.new',
            ],
            'second_options' => [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'password.new.confirm',
            ],
            'invalid_message' => $this->translator->trans('password.not_same'),
            // Instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
        ]);
    }

    public function getParent(): string
    {
        return RepeatedType::class;
    }

}