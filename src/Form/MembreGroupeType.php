<?php

namespace App\Form;

use App\Entity\EvenementEcologique;
use App\Entity\MembreGroupe;
use App\Entity\Groupe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\StatutMembre; // ← C’EST CETTE LIGNE QUI MANQUE !

class MembreGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%s (%s)', $user->getEmail(), $user->getPrenom() . ' ' . $user->getNom());
                },
                'label' => 'Utilisateur',
                'placeholder' => 'Sélectionner un utilisateur',
                'attr' => ['class' => 'form-select'],
                'required' => true,
            ])
            ->add('idEvenement', EntityType::class, [
                'class' => EvenementEcologique::class,
                'choice_label' => 'titre',
                'label' => 'Evenement',
                'placeholder' => 'Sélectionner un événement',
                'attr' => [
                    'class' => 'form-select',
                    'id' => 'evenement-select', // pour le JS
                ],
                'required' => true,
            ])
            ->add('idGroupe', EntityType::class, [
                'class' => Groupe::class,
                'choice_label' => 'nom',
                'label' => 'Groupe lie a l evenement',
                'placeholder' => 'Sélectionner un groupe',
                'attr' => [
                    'class' => 'form-select',
                ],
                'required' => true,
            ])
           /* ->add('dateAdhesion', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date d adhesion',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])*/
            ->add('statut', EnumType::class, [
                'class' => StatutMembre::class,
                'choice_label' => fn($statut) => $statut->label(),
                'label' => 'Statut du membre',
                'placeholder' => 'Sélectionner un statut',
                'attr' => ['class' => 'form-select'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MembreGroupe::class,
        ]);
    }
}
