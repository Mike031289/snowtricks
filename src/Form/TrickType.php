<?php
// src/Form/TrickType.php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder

            // ===== TRICK NAME =====
            ->add('name', null, [
                'label' => 'Nom de la figure',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de la figure est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])

            // ===== DESCRIPTION =====
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La description est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])

            // ===== MAIN IMAGE =====
            ->add('mainImage', FileType::class, [
                'label' => 'Image principale',
                'mapped' => false,
                'required' => !$isEdit,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'L’image ne doit pas dépasser 2 Mo.',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Formats autorisés : JPG, PNG, WEBP.',
                    ]),
                    ...(!$isEdit ? [
                        new Assert\NotBlank([
                            'message' => 'Une image principale est obligatoire.',
                        ])
                    ] : []),
                ],
            ])

            // ===== ADDITIONAL IMAGES =====
            ->add('images', FileType::class, [
                'label' => 'Images supplémentaires',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])

            // ===== VIDEOS =====
            ->add('videos', TextareaType::class, [
                'label' => 'Vidéos (YouTube)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://youtube.com/watch?v=xxx',
                ],
            ])

            // ===== GROUP =====
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un groupe',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner un groupe.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'is_edit' => false,
        ]);
    }
}