<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\RoleUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Personal Information Section
            ->add('nom', TextType::class, [
                'label' => 'Last Name',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter last name',
                    'maxlength' => 255
                ],
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First Name',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter first name',
                    'maxlength' => 255
                ],
                'required' => true
            ])
            
            // Contact Information Section
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter email address',
                    'maxlength' => 255
                ],
                'required' => true
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Phone Number',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter phone number',
                    'maxlength' => 20
                ],
                'required' => true
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Address',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter full address',
                    'maxlength' => 500
                ],
                'required' => true
            ])
            
            // Security Section
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter secure password',
                    'minlength' => 8
                ],
                'required' => true,
                'help' => 'Password must be at least 8 characters long'
            ])
            
            // Role Section
            ->add('role', EnumType::class, [
                'class' => RoleUser::class,
                'choice_label' => function (RoleUser $role) {
                    return $role->label();
                },
                'label' => 'User Role',
                'placeholder' => 'Select a role',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Choose the appropriate role for this user'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}