<?php

// src/Form/StaffTicketType.php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaffTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'label' => 'Changer le statut'
            ])
            ->add('responsible', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Assigner à',
                'required' => false,
                'placeholder' => 'Non assigné'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}