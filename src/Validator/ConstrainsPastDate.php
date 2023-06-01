<?php

// src/Validator/ContainsPastDate.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

// Définition de la classe de contrainte personnalisée
#[\Attribute]
class ConstainsPastDate extends Constraint
{   
    // Utilisation de l'attribut HasNamedArguments pour permettre des arguments nommés
    #[HasNamedArguments]
    // Constructeur de la classe
    public function __construct(
        // Paramètre : mode de validation de la date (valeur par défaut : 'strict')
        public string $mode = 'strict',
        // Paramètre : message de validation (par défaut, une date future est invalide)
        public string $message = 'La date "{{ string }}" est invalide, veuillez sélectionner une date passée.',
        // Paramètre optionnel : groupes de validation
        array $groups = null,
        // Paramètre optionnel : payload supplémentaire
        mixed $payload = null,
    ) {
        // Appel du constructeur parent de la classe Constraint
        parent::__construct([], $groups, $payload);
    }
}