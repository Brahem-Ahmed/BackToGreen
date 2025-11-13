<?php

namespace App\Form;

use App\Entity\CollecteDechet;
use App\Entity\User;
use App\Entity\ZoneCollecte;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollecteDechetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCollecte')
            ->add('quantite')
            ->add('typeDechet')
            ->add('statut')
            ->add('idZone', EntityType::class, [
                'class' => ZoneCollecte::class,
                'choice_label' => 'id',
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
            'data_class' => CollecteDechet::class,
        ]);
    }
}
