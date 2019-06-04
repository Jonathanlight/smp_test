<?php

namespace App\Form;

use App\Entity\Center;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
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

        $builder->add('name', TextType::class, [
                'label' => 'info.place.nameLieu',
                'attr' => [
                    'placeholder' => 'info.place.nameLieu',
                ],
            ])
            ->add('equipments', ChoiceType::class, [
                'label' => 'info.place.equipement',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'equipment.type.restaurant' => Place::EQUIPMENT_RESTAURANT,
                    'equipment.type.restaurant_nearby' => Place::EQUIPMENT_RESTAURANT_NEARBY,
                    'equipment.type.access_disabled' => Place::EQUIPMENT_ACCESS_DISABLED,
                    'equipment.type.access_transport' => Place::EQUIPMENT_ACCESS_TRANSPORT,
                    'equipment.type.parking' => Place::EQUIPMENT_PARKING,
                    'equipment.type.parking_free' => Place::EQUIPMENT_PARKING_FREE,
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'info.place.adresse',
                'attr' => [
                    'required' => false,
                    'placeholder' => 'info.place.adresse',
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
            ->add('submit', SubmitType::class, [
                'label' => 'form.action.submit',
                'attr' => [
                    'class' => 'btn btn-secondary',
                ],
            ])
        ;
    }
}
