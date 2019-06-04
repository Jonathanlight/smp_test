<?php

namespace App\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportingSearchType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'user' => null,
           'csrf_protection' => false,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startOn', DateType::class, [
                'required' => false,
                'label' => 'info.datedebut',
                'html5' => false,
                'attr' => ['class' => 'jsdatepicker'],
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
            ])
            ->add('endOn', DateType::class, [
                'required' => false,
                'label' => 'info.datefin',
                'html5' => false,
                'attr' => ['class' => 'jsdatepicker'],
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
            ])
            ->setMethod(Request::METHOD_GET)
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
