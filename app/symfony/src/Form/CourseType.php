<?php

namespace App\Form;

use App\Entity\Animator;
use App\Entity\Center;
use App\Entity\Course;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
            'user' => null,
            'course' => null,
            'comment' => false,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (User::ROLE_CSSR !== $options['user']->getRole()) {
            $builder->add('comment', TextareaType::class, [
                'label' => 'info.stage.comment',
                'required' => false,
            ]);
        }

        if (!$options['comment']) {
            $builder
                ->add('amount', NumberType::class, [
                    'label' => 'info.stage.amount',
                ])
                ->add('startOn', DateType::class, [
                    'label' => 'info.stage.start_on',
                    'html5' => false,
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy',
                    'attr' => [
                        'class' => 'jsdatepicker',
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('endOn', DateType::class, [
                    'label' => 'info.stage.end_on',
                    'html5' => false,
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy',
                    'attr' => [
                        'class' => 'jsdatepicker',
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('startMorningStartAt', TimeType::class, [
                    'label' => 'info.stage.start_morning_start_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('startMorningEndAt', TimeType::class, [
                    'label' => 'info.stage.end_morning_end_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('startAfternoonStartAt', TimeType::class, [
                    'label' => 'info.stage.end_afternoon_start_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('startAfternoonEndAt', TimeType::class, [
                    'label' => 'info.stage.end_afternoon_end_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('endMorningStartAt', TimeType::class, [
                    'label' => 'info.stage.end_morning_start_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('endMorningEndAt', TimeType::class, [
                    'label' => 'info.stage.start_afternoon_end_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('endAfternoonStartAt', TimeType::class, [
                    'label' => 'info.stage.start_afternoon_start_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('endAfternoonEndAt', TimeType::class, [
                    'label' => 'info.stage.start_morning_end_at',
                    'widget' => 'single_text',
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('special', CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'disabled' => null !== $options['course']->getId(),
                    ],
                ])
                ->add('quantity', IntegerType::class, [
                    'label' => 'info.stage.quantity',
                    'attr' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ]);

            if ($options['user']->getCenter() instanceof Center) {
                $builder
                    ->add('former', EntityType::class, [
                        'label' => 'info.stage.former',
                        'required' => false,
                        'class' => Animator::class,
                        'placeholder' => 'info.stage.placeholder.former',
                        'query_builder' => function (EntityRepository $er) use ($options) {
                            return $er->createQueryBuilder('a')
                                ->orderBy('a.email', 'ASC')
                                ->andWhere('a.degree = :type')
                                ->andWhere('a.center = :center')
                                ->andWhere('a.deletedAt IS NULL')
                                ->setParameter('type', Animator::DEGREE_BAFM_BAFCRI)
                                ->setParameter('center', $options['user']->getCenter());
                        },
                    ])
                    ->add('place', EntityType::class, [
                        'label' => 'info.stage.place',
                        'class' => Place::class,
                        'placeholder' => 'info.stage.placeholder.place',
                        'query_builder' => function (EntityRepository $er) use ($options) {
                            return $er->createQueryBuilder('p')
                                ->andWhere('p.center = :center')
                                ->andWhere('p.deletedAt IS NULL')
                                ->setParameter('center', $options['user']->getCenter())
                                ->orderBy('p.name', 'ASC');
                        },
                        'attr' => [
                            'disabled' => null !== $options['course']->getId() && $options['course']->getOrders()->count(),
                        ],
                    ])
                    ->add('psychologist', EntityType::class, [
                        'label' => 'info.stage.psychologist',
                        'required' => false,
                        'class' => Animator::class,
                        'placeholder' => 'info.stage.placeholder.psychologist',
                        'query_builder' => function (EntityRepository $er) use ($options) {
                            return $er->createQueryBuilder('a')
                                ->orderBy('a.email', 'ASC')
                                ->andWhere('a.degree = :type')
                                ->andWhere('a.deletedAt IS NULL')
                                ->andWhere('a.center = :center')
                                ->setParameter('type', Animator::DEGREE_PSYCHOLOGIST)
                                ->setParameter('center', $options['user']->getCenter());
                        },
                    ]);
            } else {
                $builder
                    ->add('former', EntityType::class, [
                        'label' => 'info.stage.former',
                        'required' => false,
                        'class' => Animator::class,
                        'placeholder' => 'info.stage.placeholder.former',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('a')
                                ->orderBy('a.email', 'ASC')
                                ->andWhere('a.degree = :type')
                                ->andWhere('a.deletedAt IS NULL')
                                ->setParameter('type', Animator::DEGREE_BAFM_BAFCRI);
                        },
                    ])
                    ->add('place', EntityType::class, [
                        'label' => 'info.stage.place',
                        'class' => Place::class,
                        'placeholder' => 'info.stage.placeholder.place',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('p')
                                ->andWhere('p.deletedAt IS NULL')
                                ->orderBy('p.name', 'ASC');
                        },
                        'attr' => [
                            'readonly' => null !== $options['course']->getId(),
                        ],
                    ])
                    ->add('psychologist', EntityType::class, [
                        'label' => 'info.stage.psychologist',
                        'required' => false,
                        'class' => Animator::class,
                        'placeholder' => 'info.stage.placeholder.psychologist',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('a')
                                ->orderBy('a.email', 'ASC')
                                ->andWhere('a.degree = :type')
                                ->andWhere('a.deletedAt IS NULL')
                                ->setParameter('type', Animator::DEGREE_PSYCHOLOGIST);
                        },
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
