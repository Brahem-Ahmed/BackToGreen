<?php

namespace App\Form;
use App\Entity\PrioriteReclamation;
use App\Entity\StatutReclamation;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ReclamationType extends AbstractType
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
            //->add('dateReclamation')
            ->add('reponse', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'style' => 'min-height: 100px;',
                    'placeholder' => 'Enter response...'
                ],
                'label' => 'Response',
                
            ])
            ->add('priorite', EnumType::class, [
                'class' => PrioriteReclamation::class,
                'choice_label' => function (PrioriteReclamation $priorite) {
                    return $priorite->label();
                },
                'label' => 'priorite',
                'placeholder' => 'Select a priorite',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Choose the appropriate role for this priorite'
            ])
            //->add('priorite')
            //->add('statut')

            ->add('statut', EnumType::class, [
                'class' => StatutReclamation::class,
                'choice_label' => function (StatutReclamation $statut) {
                    return $statut->label();
                },
                'label' => 'statut',
                'placeholder' => 'Select a statut',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Choose the appropriate role for this statut'
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
