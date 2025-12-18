<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\EvenementEcologique;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire', TextareaType::class, [
                'attr' => [
                    'rows' => 10,
                    'class' => 'form-control',
                    'placeholder' => 'Enter your review comment here...'
                ],
                'label' => 'Commentaire',
                'required' => true,
                'empty_data' => '',
            ])
            ->add('note')
            //->add('dateAvis')
  
            ->add('idEvenement', EntityType::class, [
                'class' => EvenementEcologique::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
