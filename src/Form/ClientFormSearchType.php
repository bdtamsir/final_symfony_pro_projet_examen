<?php

namespace App\Form;

use App\Dto\ClientFormSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientFormSearchType extends AbstractType
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
        ->add('statut', ChoiceType::class, [
            'required' => false,
            'label' => false,
            'choices' => [
                'Tout'=> 'Tout',
                'Oui'=> 'Oui',
                'Non'=> 'Non',
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
            'data_class' => ClientFormSearch::class,
        ]);
    }
}
