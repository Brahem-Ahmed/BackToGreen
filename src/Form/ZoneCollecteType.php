<?php

namespace App\Form;

use App\Entity\ZoneCollecte;
use App\Entity\TypeDechet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ZoneCollecteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Location Information Section
            ->add('nom', TextType::class, [
                'label' => 'Zone Name',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter zone name',
                    'maxlength' => 255
                ],
                'required' => true,
                'help' => 'Unique name for this collection zone'
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Address',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter full address',
                    'maxlength' => 255
                ],
                'required' => true,
                'help' => 'Complete physical address of the collection zone'
            ])
            
            // Geographic Coordinates Section
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., 48.8566',
                    'step' => '0.000001',
                    'min' => '-90',
                    'max' => '90'
                ],
                'required' => true,
                'help' => 'Geographic latitude coordinate (decimal format, -90 to 90)'
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., 2.3522',
                    'step' => '0.000001',
                    'min' => '-180',
                    'max' => '180'
                ],
                'required' => true,
                'help' => 'Geographic longitude coordinate (decimal format, -180 to 180)'
            ])
            
            // Capacity & Operations Section
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacity (kg)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter capacity in kilograms',
                    'min' => '1'
                ],
                'required' => true,
                'help' => 'Maximum storage capacity in kilograms'
            ])
            ->add('horaires', TextType::class, [
                'label' => 'Operating Hours',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., Mon-Fri: 08:00-18:00, Sat: 09:00-14:00',
                    'maxlength' => 255
                ],
                'required' => true,
                'help' => 'Operating hours and days for this collection zone'
            ])
            
            // Waste Type Section
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
                'help' => 'Primary waste type collected at this zone'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ZoneCollecte::class,
        ]);
    }
}
