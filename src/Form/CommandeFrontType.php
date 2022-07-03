<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class CommandeFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre' , ChoiceType::class , array('data_class'   =>  null))
            //->add('description')
            //->add('couleur')
            //->add('collection' , ChoiceType::class , [
                //"choices" => [
                    //"Femme" => "Femme",
                    //"Homme" => "Homme"
                //], 
                //"placeholder" => "Choisir..."
            //])
            //->add('photo' , FileType::class , 
            //["mapped" => false , 
            //"required" => false])
            //->add('prix' , MoneyType::class)
            //->add('stock')
            ->add('quantite' ,  ChoiceType::class , array('data_class'   =>  null))
            // ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
