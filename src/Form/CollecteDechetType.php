<?php

namespace App\Form;

use App\Entity\CollecteDechet;
use App\Entity\User;
use App\Entity\ZoneCollecte;
use App\Entity\TypeDechet;
use App\Entity\StatutCollecte;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class CollecteDechetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Collection Details Section
            ->add('dateCollecte', DateTimeType::class, [
                'label' => 'Collection Date & Time',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'datetime-local'
                ],
                'required' => true,
                'help' => 'Date and time when waste collection occurred'
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantity (kg)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter quantity in kilograms',
                    'step' => '0.01',
                    'min' => '0'
                ],
                'required' => true,
                'help' => 'Amount of waste collected in kilograms'
            ])
            
            // Waste Information Section
            ->add('typeDechet', EnumType::class, [
                'class' => TypeDechet::class,
                'choice_label' => function (TypeDechet $type) {
                    return $type->label();
                },
                'label' => 'Type of Waste',
                'placeholder' => 'Select waste type',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Category of waste collected'
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutCollecte::class,
                'choice_label' => function (StatutCollecte $status) {
                    return $status->label();
                },
                'label' => 'Collection Status',
                'placeholder' => 'Select status',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Current status of this collection activity'
            ])
            
            // Related Information Section
            ->add('idZone', EntityType::class, [
                'class' => ZoneCollecte::class,
                'choice_label' => 'nom',
                'label' => 'Collection Zone',
                'placeholder' => 'Select collection zone',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Zone where waste was collected'
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%s %s', $user->getPrenom(), $user->getNom());
                },
                'label' => 'Collector User',
                'placeholder' => 'Select user',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => false,
                'help' => 'User responsible for this collection (optional)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollecteDechet::class,
        ]);
    }
}
