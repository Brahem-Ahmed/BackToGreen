<?php

namespace App\Form;

use App\Entity\EvenementEcologique;
use App\Entity\Participation;
use App\Entity\User;
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
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return sprintf('%s %s (%s)', $user->getPrenom(), $user->getNom(), $user->getEmail());
                },
                'label' => 'User',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('dateInscription', DateTimeType::class, [
                'label' => 'Registration Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Status',
                'choices' => StatutParticipation::cases(),
                'choice_label' => fn(StatutParticipation $s) => $s->label(),
                'choice_value' => fn(?StatutParticipation $s) => $s?->value,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('idEvenement', EntityType::class, [
                'class' => EvenementEcologique::class,
                'choice_label' => function(EvenementEcologique $event) {
                    return sprintf('%s - %s', $event->getTitre(), $event->getDateDebut()->format('Y-m-d'));
                },
                'label' => 'Event',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Comments',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
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
