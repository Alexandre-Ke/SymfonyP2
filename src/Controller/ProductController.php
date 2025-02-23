<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\CsvExporter;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product', methods: ['GET'])]
    public function index(ProductRepository $productRepository, Request $request): Response
    {

        $sortOrder = $request->query->get('sort', 'ASC');

        $products = $productRepository->findAllSortedByPrice($sortOrder);

        return $this->render('product/index.html.twig', [
            // 'products' => $productRepository->findAll(),
            'products' => $products,
            'sortOrder' => $sortOrder
        ]);
    }

    #[Route('/new/product', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new Product();
        $form = $this->createForm(ProductFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');

            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Produit mis à jour avec succès.');

            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide. Suppression annulée.');
        }

        return $this->redirectToRoute('app_product');
    }


    #[Route('/product/download/csv', name: 'app_product_download_csv', methods: ['GET'])]
    public function downloadCsv(CsvExporter $csvExporter): StreamedResponse
    {
        return $csvExporter->exportProductsCsv();
    }
}
