<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    // #[Security("is_granted('POST_SHOW', post)", statusCode: 404, message: 'Resource not found.')]
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, RateLimiterFactory $anonymousApiLimiter): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on créé un limiter pour l'IP du client (ou un autre identifiant unique)
            // (par exemple, on peut utiliser le User-Agent, l'adresse email, etc.)
            $limiter = $anonymousApiLimiter->create($request->getClientIp());

            // la configuration du limiter est définie dans config/packages/framework.yaml
            // ici on utilise la limite "anonymous_api" qui permet 60 requêtes par minute

            // on peut utiliser la méthode consume() qui consomme une unité de la limite
            // et retourne un objet Limit qui contient le nombre de requêtes restantes et le temps d'attente
            if (false === $limiter->consume(1)->isAccepted()) {
                throw new TooManyRequestsHttpException();
            }

            // on peut aussi utiliser la méthode ensureAccepted() qui lance une exception si le nombre de requêtes est dépassé
            // $limiter->consume(1)->ensureAccepted();

            // pour réinitialiser le compteur on peut utiliser la méthode reset()
            // $limiter->reset();

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
