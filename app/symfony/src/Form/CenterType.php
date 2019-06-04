<?php

namespace App\Form;

use App\Entity\Center;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CenterType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Center::class,
            'user' => null,
            'center' => null,
            'is_new' => false,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'info.default.nomSociale',
            ])
            ->add('address', TextType::class, [
                'label' => 'info.default.adresse',
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
                'label' => 'form.action.submit', 'attr' => [
                    'class' => 'btn btn-secondary',
                ],
            ])
            ->add('users', CollectionType::class, [
                'entry_type' => UserType::class,
                'entry_options' => [
                    'label' => false,
                    'passwordRequired' => false !== $options['is_new'],
                ],
                'label' => false,
            ])
        ;

        if (false === $options['is_new']) {
            if (User::ROLE_CSSR !== $options['user']->getRole()) {
                $builder->add('tarif', TarifType::class, [
                    'label' => false,
                    'center' => $options['center'],
                ]);
            }
            $builder
                ->add('apeCode', TextType::class, [
                    'label' => 'info.default.codeAPE',
                    'required' => false,
                ])
                ->add('approvalNumber', TextType::class, [
                    'label' => 'info.default.agrement',
                ])
                ->add('bankName', TextType::class, [
                    'label' => 'info.default.nomBanque',
                ])
                ->add('capital', IntegerType::class, [
                    'label' => 'info.center.capital',
                ])
                ->add('juridiqueForme', TextType::class, [
                    'label' => 'info.center.juridiqueForm',
                ])
                ->add('bankOwner', TextType::class, [
                    'label' => 'info.default.titulaireCompte',
                ])
                ->add('bic', TextType::class, [
                    'label' => 'info.default.codeBic',
                ])
                ->add('iban', TextType::class, [
                    'label' => 'info.default.iban',
                ])
                ->add('capital', TextType::class)
                ->add('juridiqueForme', TextType::class)
                ->add('email', TextType::class, [
                    'label' => 'info.default.email2',
                    'required' => false,
                ])
                ->add('fax', TextType::class, [
                    'label' => 'info.default.fax',
                    'required' => false,
                ])
                ->add('firstName', TextType::class, [
                    'label' => 'info.default.prenom',
                    'required' => false,
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'info.default.nom',
                    'required' => false,
                ])
                ->add('mobile', TextType::class, [
                    'label' => 'info.default.portable',
                    'required' => false,
                ])
                ->add('phone', TextType::class, [
                    'label' => 'info.default.fix',
                    'required' => false,
                ])
                ->add('prefecture', TextType::class, [
                    'label' => 'info.default.delivrePrefecture',
                    'required' => false,
                ])
                ->add('rcs', TextType::class, [
                    'label' => 'info.default.villeRCS',
                    'required' => false,
                ])
                ->add('siret', TextType::class, [
                    'label' => 'info.default.siret',
                    'required' => false,
                ])
                ->add('vatNumber', TextType::class, [
                    'label' => 'info.default.intracommunautaireTVA',
                    'required' => false,
                ])
            ;

            if (User::ROLE_CSSR !== $options['user']->getRole()) {
                $builder->add('comment', TextareaType::class, [
                    'label' => 'info.default.comment',
                    'required' => false,
                ]);
            }
        }
    }
}
