<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // Repeated password field (password + confirmation)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        // Browser autocomplete hint for security
                        'autocomplete' => 'new-password',
                    ],
                ],

                // First password input (main password)
                'first_options' => [
                    'label' => 'Nouveau mot de passe',

                    // Validation rules for the first field
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe',
                        ]),
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                        new PasswordStrength([
                            'message' => 'Le mot de passe est trop faible',
                        ]),
                        new NotCompromisedPassword([
                            'message' => 'Ce mot de passe a été compromis dans une fuite de données',
                        ]),
                    ],
                ],

                // Second password input (confirmation field)
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                ],
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',

                // Field is not directly mapped to the entity
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}