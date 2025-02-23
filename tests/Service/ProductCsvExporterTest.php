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
        $product = new Product();
        $product->setName('Produit Test')
                ->setDescription('Description du produit test')
                ->setPrice(29.99);

        $reflection = new \ReflectionClass($product);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($product, 1);

        $productRepositoryMock = $this->createMock(ProductRepository::class);
        $productRepositoryMock->method('findAll')
                              ->willReturn([$product]);

        $productCsvExporter = new ProductCsvExporter($productRepositoryMock);

        $response = $productCsvExporter->exportProductsCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $expectedCsv = "ID;Nom;Description;\"Prix (€)\"\n" .
                       "1;\"Produit Test\";\"Description du produit test\";29.99\n";

        $this->assertEquals($expectedCsv, $csvContent);
    }
}
