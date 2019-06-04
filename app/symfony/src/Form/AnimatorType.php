<?php

namespace App\Form;

use App\Entity\Animator;
use App\Entity\Center;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimatorType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animator::class,
            'user' => null,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (User::ROLE_CSSR !== $options['user']->getRole() && null === $options['user']->getCenter()) {
            $builder->add('center', EntityType::class, [
                'label' => 'form.animator.center',
                'placeholder' => 'form.animator.center',
                'class' => Center::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.deletedAt IS NULL');
                },
            ]);
        }
        $builder
            ->add('degree', ChoiceType::class, [
                'label' => 'form.animator.degree',
                'choices' => [
                    'form.animator.bafm' => Animator::DEGREE_BAFM_BAFCRI,
                    'form.animator.psychologist' => Animator::DEGREE_PSYCHOLOGIST,
                ],
                'expanded' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.animator.email',
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'form.animator.nom',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.animator.prenom',
            ])
            ->add('mobile', TelType::class, [
                'label' => 'form.animator.mobile',
                'required' => false,
            ])
            ->add('phone', TelType::class, [
                'label' => 'form.animator.phone',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label' => 'form.animator.addresse',
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
            ->add('postalCode', HiddenType::class, [
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
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.action.submit',
                'attr' => [
                    'class' => 'btn btn-secondary',
                ],
            ])
        ;
    }
}
