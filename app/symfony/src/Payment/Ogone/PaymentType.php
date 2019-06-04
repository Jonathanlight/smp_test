<?php

namespace App\Payment\Ogone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaymentModel::class,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('AMOUNT', HiddenType::class)
            ->add('CURRENCY', HiddenType::class)
            ->add('EMAIL', HiddenType::class)
            ->add('LANGUAGE', HiddenType::class)
            ->add('ORDERID', HiddenType::class)
            ->add('PSPID', HiddenType::class)
            ->add('SHASIGN', HiddenType::class)
            ->add('HOMEURL', HiddenType::class, [
                'mapped' => false,
                'data' => 'NONE',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.payment.submit',
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
