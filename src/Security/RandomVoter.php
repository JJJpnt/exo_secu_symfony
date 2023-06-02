<?php

// src/Security/ProductVoter.php
namespace App\Security;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

// Un Voter bidon qui lance un dé pour décider si l'utilisateur peut accéder à une ressource
class RandomVoter extends Voter
{
    // ces constantes sont utilisées pour identifier les actions, on peut les nommer comme on veut
    const VIEW = 'view';

    // cette méthode est appelée pour chaque requête, $attribute correspond à une des constantes ci-dessus
    protected function supports(string $attribute, mixed $subject): bool
    {
        // si l'action n'est pas supportée, on retourne false
        if (!in_array($attribute, [self::VIEW])) {
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
        // $user = $token->getUser();

        // if (!$user instanceof User) {
        //     // the user must be logged in; if not, deny access
        //     return false;
        // }

        // tu sais que $subject est une instance de Product, grâce à la méthode supports()
        /** @var Product $product */
        $product = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($product),
            default => throw new \LogicException('Cette ligne ne devrait logiquement pas être exécutée')
        };
    }

    private function canView(Product $product): bool
    {
        // Lancer de dé
        if($random = rand(1, 6) > 3) {
            return true;
        } else {
            return false;
        }
    }

}