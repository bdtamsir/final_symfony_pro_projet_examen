<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NoAccountClientSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('surname', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Surname'
            ]
        ])
        ->add('telephone', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Téléphone'
            ]
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
