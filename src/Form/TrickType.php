<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Determine if the form is used for editing an existing Trick
        // This allows us to make some fields optional (e.g. main image)
        $isEdit = $options['is_edit'] ?? false;

        $builder

            // Trick name field
            ->add('name', null, [
                'label' => 'name',
                'required' => true, // HTML required attribute
                'constraints' => [
                    // Ensure the field is not empty (server-side validation)
                    new NotBlank([
                        'message' => 'Le nom est obligatoire.',
                    ]),
                    // Ensure a minimum length for better data quality
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
                        'max' => 255,
                    ]),
                ],
            ])

            // Trick description field
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description est obligatoire.',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'La description doit comporter au moins {{ limit }} caractères.',
                    ]),
                ],
            ])

            // Main image upload field
           ->add('mainImage', FileType::class, [
                'label' => 'Image principale',
                'mapped' => false,
                'required' => !$isEdit,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser 2 Mo.',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG).',
                    ]),
                    ...(!$isEdit ? [
                        new NotBlank([
                            'message' => 'Une image principale est requise.',
                        ])
                    ] : [])
                ],
            ])

            // Additional images (multiple upload)
            ->add('images', FileType::class, [
                'label' => 'Images supplémentaires.',
                'multiple' => true,
                'mapped' => false, // Not directly linked to the entity
                'required' => false,
            ])

            // Videos field (YouTube URLs)
            ->add('videos', TextareaType::class, [
                'label' => 'Videos (YouTube ou Autres)',
                'attr' => [
                    // Placeholder to guide the user on expected input format
                    'placeholder' => 'https://youtube.com/watch?v=xxx, https://youtu.be/xxx',
                ],
                'mapped' => false,
                'required' => false,
            ])

            // Group selection (dropdown)
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name', // Display the group name in the dropdown
                'placeholder' => 'Choisissez un groupe',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un groupe.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'is_edit' => false, // Default mode is "create"
        ]);
    }
}