<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextAreaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description',
                    ])
                ],            
            ])
            ->add('adultContent')
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
        ;

        $builder->get('description')
            // Pour transformer les données avant de les enregistrer en base de données, on peut utiliser un DataTransformer
            // Ici on les nettoie simplement, mais on pourrait aussi les transformer (ex: date au format français vers le format anglais, ...)
            // https://symfony.com/doc/current/form/data_transformers.html       
            // On utilise ici un CallbackTransformer pour l'intégrer directement dans le formulaire, 
            // mais on peut aussi créer une classe de DataTransformer personnalisée et réutilisable (voir doc)
            // Le premier argument de CallbackTransformer est une fonction qui sera appelée pour transformer les données pour l'affichage dans le formulaire
            // Le deuxième argument est une fonction qui sera appelée pour transformer les données avant de les enregistrer en base de données
            ->addModelTransformer(new CallbackTransformer(
                // Transformation des données pour l'affichage dans le formulaire
                function ($value)
                {
                    // Transformation facultative des données pour l'affichage dans le formulaire
                    // Vous pouvez laisser cette méthode vide si aucune transformation n'est nécessaire
                    return $value;
                },
                function ($value)
                {
                    // Validation, nettoyage et transformation des données avant de les enregistrer en base de données
                    // Exemple : suppression des balises HTML, suppression des espaces vides, htmlspecialchars, etc.
                    $cleanedValue = strip_tags(trim($value));
                    // Autres validations ou transformations nécessaires, exemple : une liste de mots interdits
                    $blacklistedWords = ['chien', 'waf', 'toutou', 'clébard', 'cabot', 'médor', 'pitbull', 'rottweiler', 'dogue'];
                    $cleanedValue = str_ireplace($blacklistedWords, '[CENSURE-ANTI-CANIDES]', $cleanedValue);
                    return $cleanedValue;
                }
            ));


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
