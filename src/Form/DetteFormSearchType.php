<?php

namespace App\Form;

use App\Dto\DetteFormSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DetteFormSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('surname', TextType::class, [
                'required' => false,
                
            ])
            ->add('telephone', TextType::class, [
                'required' => false,
              
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Tout' => 'Tout',
                    'Soldées' => 'Soldées',
                    'Non Soldées' => 'Non Soldées',
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
            'data_class' => DetteFormSearch::class,
        ]);
    }

}
