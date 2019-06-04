<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Trainee;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TraineeType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trainee::class,
            'user' => null,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliveredAt', TextType::class, [
                'label' => 'info.trainee.placeDelivrance',
                'required' => false,
                'attr' => [
                    'class' => 'autocomplete',
                ],
            ])
            ->add('courseType', ChoiceType::class, [
                'required' => false,
                'label' => 'info.trainee.typeStage',
                'placeholder' => 'info.trainee.placeholder.courseType',
                'choices' => [
                    'info.trainee.type.voluntary' => Trainee::TYPE_VOLUNTARY,
                    'info.trainee.type.required' => Trainee::TYPE_REQUIRED,
                ],
            ])
            ->add('obtainedOn', DateType::class, [
                'label' => 'info.trainee.dateObtention',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'jsdatepicker',
                ],
                'format' => 'dd.MM.yyyy',
            ])
            ->add('deliveredOn', DateType::class, [
                'label' => 'info.trainee.dateDelivrance',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'jsdatepicker',
                ],
                'format' => 'dd.MM.yyyy',
            ])
            ->add('driverLicense', TextType::class, [
                'required' => false,
                'label' => 'info.trainee.driverLicense',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'info.trainee.firstname',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'info.trainee.lastname',
            ])
            ->add('phone', TextType::class, [
                'label' => 'info.trainee.phone',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'info.trainee.email',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label' => 'info.trainee.address',
                'attr' => [
                    'class' => 'autocomplete',
                ],
            ])
            ->add('placeBirth', TextType::class, [
                'label' => 'info.trainee.placeBirth',
            ])
            ->add('dateBirth', DateType::class, [
                'label' => 'info.trainee.dateBirth',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'jsdatepicker',
                ],
                'format' => 'dd.MM.yyyy',
            ])
            ->add('placeInfraction', TextType::class, [
                'required' => false,
                'label' => 'info.trainee.placeInfraction',
            ])
            ->add('datePlaceInfraction', DateType::class, [
                'required' => false,
                'label' => 'info.trainee.datePlaceInfraction',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'jsdatepicker',
                ],
                'format' => 'dd.MM.yyyy',
            ])
            ->add('hourPlaceInfraction', TimeType::class, [
                'required' => false,
                'label' => 'info.trainee.hourPlaceInfraction',
                'data' => new \DateTime(Course::TIME_START_AT),
                'widget' => 'single_text',
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
            ->add('postalCode', HiddenType::class, [
                'label' => 'info.trainee.postalCode',
                'attr' => [
                    'class' => 'postal_code',
                ],
            ])
            ->add('region', HiddenType::class, [
                'attr' => [
                    'class' => 'administrative_area_level_1',
                ],
            ])
            ->add('streetName', HiddenType::class, [
                'attr' => [
                    'class' => 'route',
                ],
            ])
            ->add('streetNumber', HiddenType::class, [
                'attr' => [
                    'class' => 'street_number',
                ],
            ]);

        if ($options['user']) {
            if (User::ROLE_SMP === $options['user']->getRole() || User::ROLE_CONSULTANT === $options['user']->getRole()) {
                $builder
                    ->add('comment', TextareaType::class, [
                        'label' => 'info.stage.comment',
                        'required' => false,
                    ]);
            }
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'form.action.submit',
                'attr' => [
                    'class' => 'btn btn-secondary',
                ],
            ])
        ;
    }
}
