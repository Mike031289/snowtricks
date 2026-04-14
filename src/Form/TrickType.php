<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        // Determine if we are in edit mode (if the trick already has an ID) to conditionally set the 'mapped' option for the mainImage field
        $isEdit = $options['is_edit'] ?? false;
    
        $builder
        
            ->add('name')
            ->add('description')
            ->add('mainImage', FileType::class, [
                'label' => 'Image principale',
                'mapped' =>  false, // this field is not directly associated with the Trick entity
                'required' => !$isEdit, // dynamically required if creating a new trick, optional if editing 
            ])
            
            ->add('images', FileType::class, [
                'label' => 'Images supplémentaires',
                'multiple' => true,
                'mapped' => false, // this field is not directly associated with the Trick entity
                'required' => false,
            ])

            ->add('videos', TextType::class, [
                'label' => 'Vidéos',
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/watch?v=xxxx'
                    ],
                'mapped' => false, // IMPORTANT (this field is not directly associated with the Trick entity and we will handle it manually in the controller)
                'required' => false,
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
            'is_edit' => false, // default value for the is_edit option
        ]);
    }
}