<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\User;
use App\Entity\EvenementEcologique;
use App\Entity\StatutParticipation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // User & Event Information Section
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return sprintf('%s %s (%s)', $user->getPrenom(), $user->getNom(), $user->getEmail());
                },
                'label' => 'User',
                'placeholder' => 'Select a user',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Choose the user who is participating'
            ])
            ->add('idEvenement', EntityType::class, [
                'class' => EvenementEcologique::class,
                'choice_label' => function(EvenementEcologique $event) {
                    return sprintf('%s - %s', $event->getTitre(), $event->getDateDebut()->format('Y-m-d'));
                },
                'label' => 'Event',
                'placeholder' => 'Select an event',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Choose the event for participation'
            ])

            // Registration Information Section
            ->add('dateInscription', DateTimeType::class, [
                'label' => 'Registration Date & Time',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
                'help' => 'Date and time when the user registered for the event'
            ])

            // Status Section
            ->add('statut', ChoiceType::class, [
                'label' => 'Participation Status',
                // use enum cases so the form binds Enum instances instead of raw strings
                'choices' => StatutParticipation::cases(),
                'choice_label' => function(StatutParticipation $s) {
                    return $s->label();
                },
                'choice_value' => function(?StatutParticipation $s) {
                    return $s?->value;
                },
                'placeholder' => 'Select a status',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Current status of the participation'
            ])

            // Additional Information Section
            ->add('commentaire', TextareaType::class, [
                'label' => 'Comments',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter any additional comments...',
                    'rows' => 3,
                    'maxlength' => 500
                ],
                'required' => false,
                'help' => 'Optional comments about this participation'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
