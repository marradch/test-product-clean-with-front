<?php

use PHPUnit\Framework\TestCase;
use App\Repository\ProductRepository;
use App\Entity\Product;

class ProductRepositoryTest extends TestCase
{
    public function testSaveCallsPdoCorrectly(): void
    {
        $pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);

        $pdo->expects($this->exactly(2))
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO'))
            ->willReturn($stmt);

        $stmt->expects($this->exactly(2))
            ->method('execute');

        $pdo->method('lastInsertId')->willReturn('123');

        $repo = new ProductRepository($pdo);

        $product = new Product(null, 'Test', 99.99, 'available', 1, ['color' => 'black']);
        $id = $repo->save($product);

        $this->assertEquals('123', $id);
    }
}