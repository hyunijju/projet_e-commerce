<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('produit' , EntityType::class , [
                'class' => Produit::class,
                'choice_label' => function ($produit) {
                return $produit->getTitre();
                }
                , 
                "placeholder" => "Choisir..."
            ]) 
            ->add('quantite')
            ->add('montant')
            ->add('etat' , ChoiceType::class , [ 
                'choices'  => [
                    'En cours de traitement' => 'En cours de traitement',
                    'Envoyé' => 'Envoyé',
                    'Livré' => 'Livré'] , 
                    "placeholder" => "Choisir..."])
            // ->add('date_enregistrement')
            
            ->add('user' , EntityType::class , [
                'class' => User::class,
                'choice_label' => 'pseudo' 
                , 
                "placeholder" => "Choisir..."
            ])
        
        ->add("save", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
