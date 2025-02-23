<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductCsvExporter
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function exportProductsCsv(): StreamedResponse
    {
        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Nom', 'Description', 'Prix (€)'], ';');

            $products = $this->productRepository->findAll();

            foreach ($products as $product) {
                

                fputcsv($handle, [
                    $product->getId(),
                    $product->getName(),
                    $product->getDescription(),
                    number_format($product->getPrice(), 2, '.', ''),
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.csv"');

        return $response;
    }
}
