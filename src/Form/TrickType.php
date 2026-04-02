<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('mainImage', FileType::class, [
                'label' => 'Image principale',
                'mapped' => false, // this field is not directly associated with the Trick entity
                'required' => true,
            ])

            // Group selection (dropdown)
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name', // name is the property to display in the dropdown. Don't use 'id' as it will show the ID instead of the name.
                'placeholder' => 'Choisir un groupe',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}