<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductCsvExporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductCsvExporterTest extends TestCase
{
    public function testExportProductsCsv(): void
    {
        // 🔥 Création d'un produit factice
        $product = new Product();
        $product->setName('Produit Test')
                ->setDescription('Description du produit test')
                ->setPrice(29.99);

        // 🔥 Utilisation de ReflectionClass pour forcer l'ID
        $reflection = new \ReflectionClass($product);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($product, 1);

        // 🔥 Mock du ProductRepository pour retourner le produit factice
        $productRepositoryMock = $this->createMock(ProductRepository::class);
        $productRepositoryMock->method('findAll')
                              ->willReturn([$product]);

        // 🔥 Création du service à tester avec le mock
        $productCsvExporter = new ProductCsvExporter($productRepositoryMock);

        // 🔥 Appel de la méthode à tester
        $response = $productCsvExporter->exportProductsCsv();

        // 🔥 Vérification du type de réponse
        $this->assertInstanceOf(StreamedResponse::class, $response);

        // 🔥 Capture du contenu du CSV généré
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // 🔥 Vérification du contenu du CSV (header + 1 produit)
        $expectedCsv = "ID;Nom;Description;\"Prix (€)\"\n" .
                       "1;\"Produit Test\";\"Description du produit test\";29.99\n";

        $this->assertEquals($expectedCsv, $csvContent);
    }
}
