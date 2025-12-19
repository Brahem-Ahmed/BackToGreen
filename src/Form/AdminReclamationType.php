<?php

namespace App\Form;

use App\Entity\PrioriteReclamation;
use App\Entity\Reclamation;
use App\Entity\StatutReclamation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AdminReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'style' => 'min-height: 120px;',
                    'placeholder' => 'Enter detailed description...'
                ],
                'label' => 'Description',
                'required' => true,
                'empty_data' => '',
            ])
            ->add('reponse', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'style' => 'min-height: 100px;',
                    'placeholder' => 'Enter response...'
                ],
                'label' => 'Response',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'min' => 5,
                        'minMessage' => 'La réponse doit contenir au moins {{ limit }} caractères.',
                    ])
                ],
            ])
            ->add('priorite', EnumType::class, [
                'class' => PrioriteReclamation::class,
                'choice_label' => function (PrioriteReclamation $priorite) {
                    return $priorite->label();
                },
                'label' => 'Priorité',
                'placeholder' => 'Select a priorite',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => false,
                'help' => 'Choose the appropriate priority for this reclamation'
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutReclamation::class,
                'choice_label' => function (StatutReclamation $statut) {
                    return $statut->label();
                },
                'label' => 'Statut',
                'placeholder' => 'Select a statut',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Choose the current status for this reclamation'
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'User',
                'placeholder' => 'Select a user',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'validation_groups' => ['Default', 'admin'],
        ]);
    }
}
