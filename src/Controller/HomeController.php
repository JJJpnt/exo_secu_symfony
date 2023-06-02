<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN')) {
            // Si l'utilisateur est un admin, on affiche tous les produits
            $products = $productRepository->findAll();
        } else {
            // Si oui, on affiche tous les produits, sinon on affiche seulement les produits non sensibles créés par l'utilisateur loggé
            $products = $productRepository->findBy(['adultContent' => $this->getUser()->isAdult()]);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
        ]);
    }

    #[Route('/voir/{id}', name: 'app_voir')]
    public function voir($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        $this->denyAccessUnlessGranted('view', $product);

        return $this->render('home/voir.html.twig', [
            'controller_name' => 'HomeController',
            'product' => $product,
        ]);
    }

}
