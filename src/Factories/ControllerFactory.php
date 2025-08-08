<?php

namespace App\Factories;

use App\Controller\{ProductController,IndexController};
use App\Repository\{ProductRepository,CategoryRepository};
use Symfony\Component\Validator\Validation;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;

class ControllerFactory
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $class)
    {
        // Сериализатор
        $normalizers = [new ObjectNormalizer(null, null, null, new PhpDocExtractor())];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($normalizers, $encoders);

        // Валидатор
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        return match ($class) {
            ProductController::class => new ProductController(
                new ProductRepository($this->pdo),
                new CategoryRepository($this->pdo),
                $validator,
                $serializer
            ),
            IndexController::class => new IndexController(
                new ProductRepository($this->pdo),
                new CategoryRepository($this->pdo),
            ),
            default => throw new \RuntimeException("Unknown controller $class"),
        };
    }
}
