<?php

namespace App\Form;

use App\Entity\Trainee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trainee::class,
            'step' => 1,
            'canSendLetter' => false,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (1 === $options['step']) {
            $builder
                ->add('civility', ChoiceType::class, [
                    'label' => 'form.step1.civility',
                    'choices' => [
                        'info.trainee.civility_ms' => Trainee::GENDER_FEMALE,
                        'info.trainee.civility_mr' => Trainee::GENDER_MALE,
                    ],
                    'multiple' => false,
                    'expanded' => true,
                    'data' => Trainee::GENDER_FEMALE,
                ])
                ->add('firstName', TextType::class, [
                    'label' => 'form.step1.first_name',
                    'required' => false,
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'form.step1.last_name',
                    'required' => false,
                ])
                ->add('dateBirth', BirthdayType::class, [
                    'label' => 'form.step1.date_birth',
                    'required' => false,
                    'format' => 'dd.MM.yyyy',
                    'years' => range(date('Y') - 99, date('Y')),
                    'placeholder' => [
                        'year' => 'form.step1.year', 'month' => 'form.step1.month', 'day' => 'form.step1.day',
                    ],
                ])
                ->add('placeBirth', TextType::class, [
                    'label' => 'form.step1.place_birth',
                    'required' => false,
                ])
                ->add('phone', TextType::class, [
                    'label' => 'form.step1.phone',
                    'required' => false,
                ])
                ->add('email', RepeatedType::class, [
                    'type' => EmailType::class,
                    'required' => false,
                    'invalid_message' => 'validator.trainee.mustMatch',
                    'first_options' => [
                        'label' => 'form.step1.email',
                    ],
                    'second_options' => [
                        'label' => 'form.step1.email_confirm',
                    ],
                ])
                ->add('address', TextType::class, [
                    'label' => 'form.step1.adresse',
                    'required' => false,
                    'attr' => [
                        'class' => 'autocomplete',
                    ],
                ])
                ->add('city', HiddenType::class, [
                    'attr' => [
                        'class' => 'locality',
                    ],
                ])
                ->add('country', HiddenType::class, [
                    'attr' => [
                        'class' => 'country',
                    ],
                ])
                ->add('department', HiddenType::class, [
                    'attr' => [
                        'class' => 'administrative_area_level_2',
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
                ->add('postal_code', HiddenType::class, [
                    'attr' => [
                        'class' => 'postal_code',
                    ],
                ])
                ->add('region', HiddenType::class, [
                    'attr' => [
                        'class' => 'administrative_area_level_1',
                    ],
                ])
                ->add('street_name', HiddenType::class, [
                    'attr' => [
                        'class' => 'route',
                    ],
                ])
                ->add('street_number', HiddenType::class, [
                    'attr' => [
                        'class' => 'street_number',
                    ],
                ])
            ;
            if (true === $options['canSendLetter']) {
                $builder
                    ->add('sendLetter', CheckboxType::class, [
                        'required' => false,
                        'label' => false,
                    ])
                ;
            }
        } elseif (2 === $options['step']) {
            $builder
                ->add('courseType', ChoiceType::class, [
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                    'choices' => [
                        'Stage volontaire de récupération de points' => Trainee::TYPE_VOLUNTARY,
                        'Stage obligatoire après réception de la lettre recommandée 48N (permis probatoire)' => Trainee::TYPE_REQUIRED,
                        'Stage obligatoire d’alternative aux poursuites ou de compositions pénale' => Trainee::TYPE_JUDICIAL,
                        'Stage obligatoire dans le cadre d’une peine complémentaire' => Trainee::TYPE_SENTENCE,
                    ],
                    'data' => Trainee::TYPE_VOLUNTARY,
                ])
                ->add('driverPoint', ChoiceType::class, [
                    'required' => false,
                    'choices' => range(1, 11),
                    'choice_label' => function ($choice) {
                        return $choice;
                    },
                ])
                ->add('driverPointUnknown', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.step2.driver_point_unknown',
                    'mapped' => false,
                ])
                ->add('placeInfraction', TextType::class, [
                    'required' => false,
                    'label' => 'form.step2.place_infraction',
                ])
                ->add('datePlaceInfraction', DateType::class, [
                    'required' => false,
                    'label' => 'form.step2.date_place_infraction',
                    'attr' => [
                        'class' => 'jsdatepicker',
                    ],
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy',
                ])
                ->add('hourPlaceInfraction', TimeType::class, [
                    'required' => false,
                    'label' => 'form.step2.hour_place_infraction',
                    'widget' => 'single_text',
                ])
                ->add('formatDriverLicense', ChoiceType::class, [
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'ANCIEN FORMAT' => Trainee::OLD_FORMAT_DRIVER_LICENCE,
                        'NOUVEAU FORMAT' => Trainee::NEW_FORMAT_DRIVER_LICENCE,
                    ],
                ])
                ->add('deliveredAt', TextType::class, [
                    'label' => 'form.step2.delivered_at',
                    'required' => false,
                ])
                ->add('deliveredOn', DateType::class, [
                    'label' => 'form.step2.delivered_on',
                    'required' => false,
                    'format' => 'dd.MM.yyyy',
                    'years' => range(date('Y') - 99, date('Y')),
                    'placeholder' => [
                        'year' => 'form.step1.year',
                        'month' => 'form.step1.month',
                        'day' => 'form.step1.day',
                    ],
                ])
                ->add('driverLicense', TextType::class, [
                    'label' => 'form.step2.driver_license',
                    'required' => false,
                ])
                ->add('obtainedOn', DateType::class, [
                    'label' => 'form.step2.obtained_on',
                    'required' => false,
                    'format' => 'dd.MM.yyyy',
                    'years' => range(date('Y') - 99, date('Y')),
                    'placeholder' => [
                        'year' => 'form.step1.year',
                        'month' => 'form.step1.month',
                        'day' => 'form.step1.day',
                    ],
                ])
                ->add('sendDriver', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.step2.send_driver',
                ])
            ;
        } elseif (3 === $options['step']) {
            $builder
                ->add('codePromo', TextType::class, [
                    'required' => false,
                    'label' => 'form.step1.promo_code',
                    'mapped' => false,
                ])
            ;
        }
    }
}
