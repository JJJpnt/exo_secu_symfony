<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        if($this->isGranted('ROLE_USER')) {
            // Si l'utilisateur est un admin, on affiche tous les produits
            $products = $productRepository->findAll();
        } else {
            // Si oui, on affiche tous les produits, sinon on affiche seulement les produits non sensibles créés par l'utilisateur loggé
            $products = $productRepository->findBy(['author' => $this->getUser(), 'adultContent' => $this->getUser()->isAdult()]);
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
