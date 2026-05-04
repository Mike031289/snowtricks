<?php

// src/Form/CommentType.php
namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CommentType extends AbstractType
{
    // Forbidden words list for comment moderation
    private const FORBIDDEN_WORDS = [
        // Direct insults
        'connard', 'connasse', 'salope', 'pute', 'putain', 'pétasse',
        'enculé', 'enculée', 'fils de pute', 'ta mère', 'ta mère la pute', 'sale merde', 'nique ta mère', 'baise ta mère', 'va te faire mettre', 'va te faire enculé', 'sale noire', 'sale noir', 'sale blanc', 'sale arabe', 'baise tes parents',
        'batard', 'bâtard', 'ordure', 'salopard', 'salope', 'salop',

        // Mild insults
        'con', 'conne','idiot', 'idiote', 'imbécile',
        'abruti', 'abrutie', 'crétin', 'crétine', 'débile',
        'nul', 'nulle', 'bouffon', 'bouffonne',

        // Racist/discriminatory terms
        'nègre', 'négresse', 'bougnoule', 'raton', 'youpin',
        'feuj', 'pédé', 'tapette', 'gouine', 'travelo',

        // Threats
        'je vais te tuer', 'je vais te buter', 'crève',
        'va mourir', 'suicide toi', 'va te pendre',

        // Spam
        'click here', 'buy now', 'casino', 'viagra',
        'free money', 'earn money',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Build forbidden words regex pattern with word boundaries
        $forbiddenPattern = implode('|', array_map(
            fn($word) => preg_quote($word, '/'),
            self::FORBIDDEN_WORDS
        ));

        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Ajoutez votre commentaire...',
                ],
                'constraints' => [
                    // Field is required
                    new Assert\NotBlank([
                        'message' => 'Le commentaire ne peut pas être vide.',
                    ]),
                    // Enforce min/max length
                    new Assert\Length([
                        'min' => 3,
                        'max' => 1000,
                        'minMessage' => 'Le commentaire doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le commentaire ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    // Block forbidden words (case-insensitive, unicode-aware, whole words only)
                    new Assert\Regex([
                        'pattern' => '/\b(' . $forbiddenPattern . ')\b/iu',
                        'match' => false,
                        'message' => 'Votre commentaire contient des termes inappropriés.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
