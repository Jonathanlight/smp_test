<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'attr' => [
                    'placeholder' => 'form.user.email',
                ],
            ])
            ->add('_password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'form.user.password',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.action.login',
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): ?string
    {
        return '';
    }
}
