<?php

namespace App\Form;

use App\Entity\PropertySearch;
use App\Entity\Option;
use App\Controller\PropertyController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minSurface', IntegerType::class, [
                'required' => false,  // ici ça veut dire qu'on peut faire une recherche sans préciser la taille minimum
                'label' => false, 
                'attr' =>[
                    'placeholder' => 'Surface minimale'
                ]
            ])

            ->add('maxPrice', IntegerType::class, [
                'required' => false,  // ici ça veut dire qu'on peut faire une recherche sans préciser la taille minimum
                'label' => false, 
                'attr' =>[
                    'placeholder' => 'Budget maximal'
                ]
            ])

            // ->add('submit', SubmitType::class, [ // ici on crée le bouton directement dans le controller
                // grafikart ne l'utilise pas car il préfère le faire directement dans le html
            //  'label'=> 'Rechercher'             // label : rechercher
            //     ]
            // )
            ->add('options', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
            'method' => 'get',
            'csrf_protection' => 'false', // méthode de token attribué à un user et qui permet d'éviter des personnes malveillantes de perturber le formulaire 
        ]);
    }

    public function getBlockPrefix()
    {
        return''; // ici on fait en sorte qu'en cas de recherche, l'URL soit épuré à la simple recherche
    }
}
