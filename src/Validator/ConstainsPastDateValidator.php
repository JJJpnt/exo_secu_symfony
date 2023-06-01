<?php

// src/Validator/ContainsPastDateValidator.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

// Définition de la classe du validateur de contrainte personnalisée
// qui va être automatiquement appelée par Symfony lors de la validation
// d'une valeur avec la contrainte personnalisée ConstainsPastDate
class ConstainsPastDateValidator extends ConstraintValidator
{
    // Méthode de validation
    public function validate($value, Constraint $constraint): void
    {
        // Vérifie si la contrainte est une instance de ConstainsPastDate
        if (!$constraint instanceof ConstainsPastDate) {
            throw new UnexpectedTypeException($constraint, ConstainsPastDate::class);
        }

        // Vérifie si la valeur est une instance de DateTime
        if (!$value instanceof \DateTime) {
            throw new UnexpectedValueException($value, 'DateTime');
        }

        $currentDateTime = new \DateTime();
        $currentDateTime->setTime(0, 0, 0); // Définit l'heure à 0:00:00
        $value->setTime(0, 0, 0); // Définit l'heure de la valeur à 0:00:00

        // Vérifie si le mode est strict ou non, puis valide la date en conséquence
        if ('strict' === $constraint->mode) {
            // Vérifie si la date est supérieure ou égale à aujourd'hui
            if ($value >= $currentDateTime) {
                // Ajoute une violation à la contexte de validation
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value->format('d-m-Y'))
                    ->addViolation();
            }
        } else {
            // Vérifie si la date est strictement supérieure à aujourd'hui
            if ($value > $currentDateTime) {
                // Ajoute une violation à la contexte de validation
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value->format('d-m-Y'))
                    ->addViolation();
            }
        }
    }
}