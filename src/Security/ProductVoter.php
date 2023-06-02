<?php

// src/Security/ProductVoter.php
namespace App\Security;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    // ces constantes sont utilisées pour identifier les actions, on peut les nommer comme on veut
    const VIEW = 'view';
    const EDIT = 'edit';

    // cette méthode est appelée pour chaque requête, $attribute correspond à une des constantes ci-dessus
    protected function supports(string $attribute, mixed $subject): bool
    {
        // si l'action n'est pas supportée, on retourne false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // on vote seulement sur les instances de Product
        if (!$subject instanceof Product) {
            return false;
        }

        return true;
    }

    // cette méthode est appelée si supports() retourne true
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // tu sais que $subject est une instance de Product, grâce à la méthode supports()
        /** @var Product $product */
        $product = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($product, $user),
            self::EDIT => $this->canEdit($product, $user),
            default => throw new \LogicException('Cette ligne ne devrait logiquement pas être exécutée')
        };
    }

    private function canView(Product $product, User $user): bool
    {
        // Si l'utilisateur est un enfant, il ne peut voir que les produits tous publics
        if(!$user->isAdult()) {
            return !$product->isAdultContent();
        } else {
            // Si l'utilisateur est un adulte, il peut voir tous les produits
            return true;
        }
    }

    private function canEdit(Product $product, User $user): bool
    {
        // Si l'utilisateur est un admin, il peut modifier tous les produits
        if($user->isAdmin()) {
            return true;
        } else {
            // Sinon, il ne peut modifier que les produits qu'il a créé et qu'il peut voir
            return $product->getAuthor() === $user && $this->canView($product, $user);
        }
    }
}