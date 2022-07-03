<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Callback;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('civilte'  , ChoiceType::class , [
                "choices" => [
                    "homme" => "Homme",
                    "femme" => "Femme"
                ], 
                "placeholder" => "Choisir..."
            ])
            ->add('pseudo')
            ->add('email')
            ->add('password')
            ->add('roles' , ChoiceType::class , [
                "mapped" => false ,
                "choices" => [
                    "user" => "ROLE_USER",
                    "admin" => "ROLE_ADMIN"
                ], 
                "placeholder" => "Choisir..."
            ])
            // ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
