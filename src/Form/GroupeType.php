<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\user;
use App\Entity\EvenementEcologique;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            //->add('dateCreation')
            ->add('evenement', EntityType::class, [
                'class' => EvenementEcologique::class,
                'choice_label' => 'titre',
                'placeholder' => 'No event',
                'required' => false,
            ])
            ->add('nombreMembres')
            ->add('idCreateur', EntityType::class, [
                'class' => user::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
        ]);
    }
}
