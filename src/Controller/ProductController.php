<?php

namespace App\Controller;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\{ProductRepository,CategoryRepository};
use Psr\Http\Message\ServerRequestInterface;
use App\DTO\{CreateProductDTO,ProductResponseDTO,UpdateProductDTO};
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use App\Entity\Product;
use App\Helper\ValidationErrorFormatter;

class ProductController
{
    public function __construct(
        private ProductRepository $repository,
        private CategoryRepository $categoryRepository,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
    ) {}

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['errors' => 'Invalid JSON'], 400);
            }

            $dto = $this->serializer->denormalize($data, CreateProductDTO::class);

            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return new JsonResponse([
                    'errors' => ValidationErrorFormatter::format($errors)
                ], 400);
            }

            if (!$this->categoryRepository->exists($dto->category_id)) {
                return new JsonResponse(['errors' => 'Категория не найдена'], 400);
            }

            $product = new Product(null, $dto->name, $dto->price, $dto->status, $dto->category_id, $dto->attributes);
            $id = $this->repository->save($product);

            return new JsonResponse(['id' => $id], 201);
        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    public function show(ServerRequestInterface $request, array $route_vars = []): ResponseInterface
    {
        try {
            $id = (int) $route_vars['id'];

            if ($id <= 0) {
                return new JsonResponse(['errors' => 'Invalid ID'], 400);
            }

            $productData = $this->repository->findById($id);

            if (!$productData) {
                return new JsonResponse(['errors' => 'Product not found'], 404);
            }

            $dto = new ProductResponseDTO(
                id: (int)$productData['id'],
                    name: $productData['name'],
                    price: (float)$productData['price'],
                    status: $productData['status'] ?? null,
                    category_id: (int)$productData['category_id'],
                    category_name: $productData['category_name'] ?? null,
                    attributes: $productData['attributes'] ?? [],
                    created_at: $productData['created_at']
                );

            return new JsonResponse($dto, 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    public function update(ServerRequestInterface $request, array $route_vars = []): ResponseInterface
    {
        try {
            $id = (int) $route_vars['id'];

            if ($id <= 0) {
                return new JsonResponse(['errors' => 'Invalid ID'], 400);
            }

            $existing = $this->repository->findById($id);

            if (!$existing) {
                return new JsonResponse(['errors' => 'Product not found'], 404);
            }

            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['errors' => 'Invalid JSON'], 400);
            }

            if (empty($data)) {
                return new JsonResponse(['errors' => 'Empty update data'], 400);
            }

            $dto = $this->serializer->denormalize($data, UpdateProductDTO::class);

            if ((int) $dto->category_id > 0 && !$this->categoryRepository->exists($dto->category_id)) {
                return new JsonResponse(['errors' => 'Категория не найдена'], 400);
            }

            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return new JsonResponse([
                    'errors' => ValidationErrorFormatter::format($errors)
                ], 400);
            }

            $updatedProduct = new Product(
                id: $id,
                    name: $dto->name ?? $existing['name'],
                    price: $dto->price ?? (float)$existing['price'],
                    status: $dto->status ?? $existing['status'],
                    categoryId: $dto->category_id ?? $existing['category_id'],
                    attributes: $dto->attributes ?? $existing['attributes']
                );

            $this->repository->update($updatedProduct);

            return new JsonResponse(['message' => 'Product updated'], 200);

        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    public function destroy(ServerRequestInterface $request, array $route_vars = []): ResponseInterface
    {
        try {
            $id = (int) $route_vars['id'];

            if ($id <= 0) {
                return new JsonResponse(['errors' => 'Invalid ID'], 400);
            }

            $product = $this->repository->findById($id);

            if (!$product) {
                return new JsonResponse(['errors' => 'Product not found'], 404);
            }

            $this->repository->delete($id);

            return new JsonResponse(['message' => 'Product deleted'], 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $filters = $request->getQueryParams();

            $sort = $filters['sort'] ?? '';
            unset($filters['sort']);

            $results = $this->repository->findAllWithFilters($filters, $sort);

            $dtoList = [];

            foreach ($results as $item) {
                $dtoList[] = new ProductResponseDTO(
                    id: $item['id'],
                    name: $item['name'],
                    price: (float)$item['price'],
                    status: $item['status'],
                    category_id: $item['category_id'],
                    category_name: $item['category_name'],
                    attributes: $item['attributes'],
                    created_at: $item['created_at']
                );
            }

            return new JsonResponse($dtoList, 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }
}