<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                'mapped' => false, // this field is not associated with any property of the Trick entity and will be handled manually in the controller
                'required' => true,
            ])

            // additional images (collection of file inputs)
            ->add('images', CollectionType::class, [
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => false,
                    'mapped' => false,
                    'required' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'mapped' => false,
                'required' => false,
                'by_reference' => false,
            ])

            // Group selection (dropdown)
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name', // name is the property to display in the dropdown. Don't use 'id' as it will show the ID instead of the name.
                'placeholder' => 'Choisir un groupe',
                'required' => true,
                'mapped' => true, // this field is associated with the 'groups' property of the Trick entity
                'multiple' => false, // only one group can be selected
                'expanded' => false, // display as a dropdown
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