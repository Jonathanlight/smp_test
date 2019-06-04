<?php

namespace App\Form\Search;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseSearchType extends BaseSearchType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

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
            ->add('pastCourses', CheckboxType::class, [
                'label' => 'info.stage.course_end',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.action.search',
            ])
        ;
    }
}
