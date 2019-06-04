<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => null,
                    'attr' => [
                        'placeholder' => 'form.user.password',
                    ],
                ],
                'second_options' => [
                    'label' => null,
                    'attr' => [
                        'placeholder' => 'form.user.password_confirmation',
                    ],
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.action.submit',
            ])
        ;
    }
}
