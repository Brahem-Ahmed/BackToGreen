<?php

namespace App\Form;

use App\Entity\PrioriteReclamation;
use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Subject',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Brief description of your complaint'
                ],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'placeholder' => 'Please provide detailed information about your complaint...'
                ],
                'required' => true,
            ])
            ->add('priorite', EnumType::class, [
                'class' => PrioriteReclamation::class,
                'label' => 'Priority',
                'choice_label' => function (PrioriteReclamation $priorite) {
                    return match($priorite) {
                        PrioriteReclamation::BASSE => 'Low',
                        PrioriteReclamation::MOYENNE => 'Medium',
                        PrioriteReclamation::HAUTE => 'High',
                        PrioriteReclamation::URGENTE => 'Urgent',
                    };
                },
                'placeholder' => 'Select priority level',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => false,
                'help' => 'How urgent is this issue?'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'validation_groups' => ['Default'], // Use Default validation group (excludes 'admin' group)
        ]);
    }
}
