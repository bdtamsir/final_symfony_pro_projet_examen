<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ArticlesFormSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('libelle', TextType::class, [
            'required' => false,
            
        ])
        ->add('etat', ChoiceType::class, [
            'choices' => [
                'Tout' => 'Tout',
                'Disponible' => 'Disponible',
                'En Rupture' => 'En Rupture',
            ],
            'label' => false,
        ])
        ->add('rechercher', SubmitType::class, [
            'attr' => ['class' => 'btn btn-dark'],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
