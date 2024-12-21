<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Client;
use App\Entity\Dette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montantVerser', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Montant Versé',
                ],
            ])
            ->add('dateAt', null, [
                'widget' => 'single_text',
                'label' => false,
            ])
            ->add('Qte', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Quantité',
                ],
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'surname',
                'label' => false,
                'placeholder' => 'Sélectionnez un client',
            ])
            ->add('articles', EntityType::class, [
                'class' => Articles::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'label' => false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dette::class,
        ]);
    }
}
