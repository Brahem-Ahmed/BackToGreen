<?php

namespace App\Form;

use App\Entity\EvenementEcologique;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EvenementEcologiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Section Informations de Base
            ->add('titre', TextType::class, [
                'label' => 'Event Title',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter event title',
                    'maxlength' => 255
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Describe the event...',
                    'rows' => 5,
                    'maxlength' => 1000
                ],
                'required' => true
            ])
            
            // Section Dates et Lieu
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Start Date & Time',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'End Date & Time',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Location',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter event location',
                    'maxlength' => 255
                ],
                'required' => true
            ])
            
            // Section Capacité et Catégorie
            ->add('capaciteMax', IntegerType::class, [
                'label' => 'Maximum Capacity',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter maximum participants',
                    'min' => 1
                ],
                'required' => true,
                'help' => 'Minimum 1 participant'
            ])
            ->add('categorie', ChoiceType::class, [
    'label' => 'Category',
    'choices' => [
        'Recycling' => 'recycling',
        'Cleaning' => 'cleaning',
        'Tree Planting' => 'tree_planting',
        'Education' => 'education',
        'Conservation' => 'conservation',
        'Other' => 'other'
    ],
    'multiple' => true, // Permettre plusieurs choix
    'placeholder' => 'Select categories', // Optionnel, mais si multiple, peut-être enlever placeholder
    'attr' => [
        'class' => 'form-select'
    ],
    'required' => true
])
            
            // Section Organisateur
            ->add('idOrganisateur', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return sprintf('%s %s (%s)', $user->getPrenom(), $user->getNom(), $user->getEmail());
                },
                'label' => 'Organizer',
                'placeholder' => 'Select an organizer',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Choose the user who will organize this event'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EvenementEcologique::class,
        ]);
    }
}