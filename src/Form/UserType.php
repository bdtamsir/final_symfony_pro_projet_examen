<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Utiliser une valeur par défaut si "exclude_fields" n'est pas défini
        $excludeFields = $options['exclude_fields'] ?? [];

        if (!in_array('login', $excludeFields)) {
            $builder->add('login', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Login'],
            ]);
        }

        if (!in_array('roles', $excludeFields)) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Boutiquier' => 'ROLE_BOUTIQUIER',
                    'Client' => 'ROLE_CLIENT',
                ],
                'expanded' => true, 
                'multiple' => true, // Va me permettre de sélectionner plusieurs rôles
                'label' => false,
                'required' => false,
            ]);
        }

        if (!in_array('password', $excludeFields)) {
            $builder->add('password', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Password'],
            ]);
        }

        if (!in_array('nom', $excludeFields)) {
            $builder->add('nom', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Nom'],
            ]);
        }

        if (!in_array('prenom', $excludeFields)) {
            $builder->add('prenom', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Prenom'],
            ]);
        }

        if (!in_array('email', $excludeFields)) {
            $builder->add('email', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Email'],
            ]);
        }

        if (!in_array('surname', $excludeFields)) {
            $builder->add('surname', TextType::class, [
                'required' => true,
                'mapped' => false, 
                'label' => false,
                'attr' => ['placeholder' => 'Surname'],
            ]);
        }

        if (!in_array('telephone', $excludeFields)) {
            $builder->add('telephone', TextType::class, [
                'required' => true,
                'mapped' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Téléphone'],
            ]);
        }

        if (!in_array('adresse', $excludeFields)) {
            $builder->add('adresse', TextType::class, [
                'required' => true,
                'mapped' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Adresse'],
            ]);
        }

        if (!in_array('save', $excludeFields)) {
            $builder->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark'],
            ]);
        }

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'exclude_fields' => [], // Définit un tableau vide par défaut
        ]);
    }
}
