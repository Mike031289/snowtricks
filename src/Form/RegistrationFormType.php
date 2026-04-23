<?php
// src/Form/RegistrationFormType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // Username field
            ->add('username', null, [
                'label' => 'Nom d\'utilisateur',
                'required' => true,
                'constraints' => [
                    // Ensure username is not empty
                    new NotBlank([
                        'message' => 'Un nom d\'utilisateur est obligatoire.',
                    ]),
                    // Enforce minimum length
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom d\'utilisateur doit comporter au moins {{ limit }} caractères.',
                        'max' => 50,
                    ]),
                ],
            ])

            // Email field
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Une adresse e-mail est obligatoire.',
                    ]),
                    // Validate proper email format
                    new Email([
                        'message' => 'Veuillez saisir une adresse e-mail valide.',
                    ]),
                ],
            ])

            // Terms agreement checkbox
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions',
                'constraints' => [
                    // Must be checked
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions.',
                    ]),
                ],
            ])

            // Plain password field (not mapped to entity directly)
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Password',
                'attr' => [
                    'autocomplete' => 'nouveau mot de passe',
                ],
                'constraints' => [
                    // Password cannot be empty
                    new NotBlank([
                        'message' => 'Veuillez choisir un mot de passe.',
                    ]),
                    // Minimum length for security
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}