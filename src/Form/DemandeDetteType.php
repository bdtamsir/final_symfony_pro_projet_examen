<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Client;
use App\Entity\DemandeDette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DemandeDetteType extends AbstractType
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'utilisateur connecté
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        // Vérifier si l'utilisateur est un Boutiquier
        $isBoutiquier = $user && in_array('ROLE_BOUTIQUIER', $user->getRoles());

        if ($isBoutiquier) {
            // Champ client visible uniquement pour le boutiquier
            $builder->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'surname', // Par exemple : afficher le "surname" du client
                'placeholder' => 'Sélectionner un client',
                'required' => true,
                'label' => false,
            ]);
        }

        $builder
            ->add('qte', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Quantité',
                ],
            ])
            ->add('articles', EntityType::class, [
                'class' => Articles::class,
                'choice_label' => 'libelle', 
                'placeholder' => 'Sélectionnez un article',
                'required' => true,
                'label' => false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeDette::class,
        ]);
    }
}
