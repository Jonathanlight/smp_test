<?php

namespace App\Form;

use App\Entity\Tarif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tarif::class,
            'center' => null,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tarif = $options['center']->getTarif();

        $builder
            ->add('commission', NumberType::class, [
                'label' => 'info.tarif.commission',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'info.tarif.typeReduction',
                'required' => false,
                'expanded' => true,
                'placeholder' => 'info.tarif.placeholder.none',
                'choices' => [
                    'info.tarif.type.course' => Tarif::TYPE_COURSE,
                    'info.tarif.type.date' => Tarif::TYPE_DATE,
                ],
            ])
            ->add('commissionByCourse', NumberType::class, [
                'label' => 'info.tarif.commissionByCourse',
                'required' => Tarif::TYPE_COURSE === $tarif->getType(),
            ])
            ->add('commissionByDate', NumberType::class, [
                'label' => 'info.tarif.commissionByDate',
                'required' => Tarif::TYPE_DATE === $tarif->getType(),
            ])
            ->add('totalCourse', IntegerType::class, [
                'label' => 'info.tarif.totalCourse',
                'required' => Tarif::TYPE_COURSE === $tarif->getType(),
            ])
            ->add('startOn', DateType::class, [
                'label' => 'info.tarif.startOn',
                'required' => Tarif::TYPE_DATE === $tarif->getType(),
                'html5' => false,
                'attr' => ['class' => 'jsdatepicker'],
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
            ])
            ->add('endOn', DateType::class, [
                'label' => 'info.tarif.endOn',
                'required' => Tarif::TYPE_DATE === $tarif->getType(),
                'html5' => false,
                'attr' => ['class' => 'jsdatepicker'],
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
            ])
        ;
    }
}
