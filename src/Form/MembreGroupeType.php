<?php

namespace App\Form;

use App\Entity\groupe;
use App\Entity\MembreGroupe;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateAdhesion')
            ->add('statut')
            ->add('idGroupe', EntityType::class, [
                'class' => groupe::class,
                'choice_label' => 'id',
            ])
            ->add('idUser', EntityType::class, [
                'class' => user::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MembreGroupe::class,
        ]);
    }
}
