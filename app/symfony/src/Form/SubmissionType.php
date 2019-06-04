<?php

namespace App\Form;

use App\Entity\Submission;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmissionType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Submission::class,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'form.submission.last_name',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'form.submission.first_name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.submission.email',
            ])
            ->add('traineeReference', TextType::class, [
                'label' => 'form.submission.trainee_reference',
                'required' => false,
            ])
            ->add('invoiceReference', TextType::class, [
                'label' => 'form.submission.invoice_reference',
                'required' => false,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.submission.message',
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'attr' => [
                    'options' => [
                        'theme' => 'light',
                        'type' => 'image',
                        'size' => 'normal',
                    ],
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submission.submit',
            ]);
    }
}
