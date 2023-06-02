<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN')) {
            // Si l'utilisateur est un admin, on affiche tous les produits
            $products = $productRepository->findAll();
        } else {
            // Si non, on affiche tous les produits, sinon on affiche seulement les produits non sensibles créés par l'utilisateur loggé
            $products = $productRepository->findBy(['author' => $this->getUser(), 'adultContent' => $this->getUser()->isAdult()]);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'utilisateur est un admin, on ne change pas l'auteur du produit
            if(!$this->isGranted('ROLE_ADMIN')) {
                $product->setAuthor($this->getUser());
            }
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER') and user.isAdult() or not product.isAdultContent()", statusCode: 403, message: 'Vous n\'avez pas le droit d\'effectuer cette action.')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or product.getAuthor() == user", statusCode: 403, message: 'Vous n\'avez pas le droit d\'effectuer cette action.')]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') or product.getAuthor() == user", statusCode: 403, message: 'Vous n\'avez pas le droit d\'effectuer cette action.')]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
