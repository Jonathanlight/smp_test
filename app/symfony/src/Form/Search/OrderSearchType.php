<?php

namespace App\Form\Search;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderSearchType extends BaseSearchType
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
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class, [
                'label' => 'info.order.nom',
                'required' => false,
            ])
            ->add('reference', TextType::class, [
                'label' => 'info.order.ref',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'info.order.status',
                'choices' => $this->getStatusByRole($options['user']),
                'required' => false,
            ])
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

    /**
     * @param User $user
     *
     * @return array
     */
    private function getStatusByRole(User $user)
    {
        $statuses = [
            'info.order.state.confirmed' => Order::STATUS_CONFIRMED,
            'info.order.state.registered' => Order::STATUS_REGISTERED,
        ];

        if (User::ROLE_CSSR !== $user->getRole()) {
            $statuses += [
                 'info.order.state.cancelled' => Order::STATUS_CANCELLED,
                 'info.order.state.refunded' => Order::STATUS_REFUNDED,
                 'info.order.state.waiting' => Order::STATUS_WAITING,
            ];
        }

        return $statuses;
    }
}
