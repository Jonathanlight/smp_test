<?php

namespace App\Form;

use App\Manager\CourseManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    const FILTER_PERTINENCE = 'pertinence';
    const FILTER_PROXIMITE = 'proximite';
    const FILTER_PRIX = 'prix';
    const FILTER_DATE = 'date';

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'advanced' => false,
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
            ->add('address', TextType::class, [
                'label' => 'info.place.adresse',
                'attr' => [
                    'placeholder' => 'info.place.adresse',
                    'class' => 'autocomplete',
                ],
            ])
            ->add('latitude', HiddenType::class, [
                'attr' => [
                    'class' => 'latitude',
                ],
            ])
            ->add('longitude', HiddenType::class, [
                'attr' => [
                    'class' => 'longitude',
                ],
            ])
            ->add('department', HiddenType::class, [
                'attr' => [
                    'class' => 'administrative_area_level_2',
                ],
            ])
            ->add('city', HiddenType::class, [
                'attr' => [
                    'class' => 'locality',
                ],
            ])
        ;

        if (true === $options['advanced']) {
            $builder
                ->add('distance', IntegerType::class, [
                    'required' => false,
                    'label' => 'form.search.filter_distance',
                    'data' => CourseManager::MAX_DISTANCE,
                    'attr' => [
                        'class' => 'slider-distance',
                    ],
                ])
                ->add('sort', ChoiceType::class, [
                    'label' => false,
                    'expanded' => true,
                    'multiple' => false,
                    'empty_data' => self::FILTER_PERTINENCE,
                    'choices' => [
                        'form.search.filter_pertinence' => self::FILTER_PERTINENCE,
                        'form.search.filter_proximite' => self::FILTER_PROXIMITE,
                        'form.search.filter_date' => self::FILTER_DATE,
                        'form.search.filter_prix' => self::FILTER_PRIX,
                    ],
                    'data' => self::FILTER_PROXIMITE,
                ])
                ->add('dayWeek', ChoiceType::class, [
                    'label' => 'form.search.day_week',
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => [
                        'form.day_week.monday' => 0,
                        'form.day_week.tuesday' => 1,
                        'form.day_week.wednesday' => 2,
                        'form.day_week.thrusday' => 3,
                        'form.day_week.friday' => 4,
                    ],
                ])
                ->add('date', DateType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'jsdatepicker',
                        'placeholder' => 'form.search.filter_date',
                    ],
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy',
                ])
                ->add('maxPrice', TextType::class, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'form.search.filter_prix_max',
                    ],
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'form.action.filtrer',
                ])
            ;
        }

        $builder->setMethod(Request::METHOD_GET);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): ?string
    {
        return '';
    }
}
