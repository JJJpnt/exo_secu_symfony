<?php

namespace App\Form;

use App\Entity\User;
use App\Service\AdresseService;
use App\Validator\ConstainsPastDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    private $adresseService;

    public function __construct(AdresseService $adresseService)
    {
        $this->adresseService = $adresseService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [  // Contraintes de validation standard
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter {{ limit }} caractères minimum',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('birthdate', DateType::class, [
                'required' => true,
                'format' => 'dd-MM-yyyy',
                'years' => range(date('Y') - 100, date('Y') + 18),
                'data' => new \DateTime('now'),
                'constraints' => [ // Ici on utilise une contrainte de validation custom
                    // si on veut faire une validation custom directement dans la classe du FormType avec une fonction
                    // new Callback([$this, 'validateBirthdate'])
                    // ou si on a créé une classe de validation custom
                    // ici je passe l'argument $mode 'permissive' au constructeur de la classe de validation custom
                    // pour permettre de s'enregistrer avec la date du jour
                    new ConstainsPastDate(mode: 'permissive')
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,  // Classe associée au formulaire
        ]);
    }

    public function validateBirthdate($value, ExecutionContextInterface $context) {
        if ($value > new \DateTime()) {
            $context->buildViolation('The birthdate should be in the past')
                ->atPath('birthdate')
                ->addViolation();
        }
    }
    
}
