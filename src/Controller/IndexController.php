<?php

namespace App\Controller;

use App\Repository\{CategoryRepository,ProductRepository};
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use App\Enums\ProductSort;

class IndexController extends AbstractFrontController
{
    public function __construct(
        private ProductRepository $repository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $categories = $this->categoryRepository->getAllWithProductCount();

            $options = ProductSort::options();

            $products = $this->repository->findAllWithFilters(['category_id' => $_GET['category_id'] ?? ''], $_GET['sort'] ?? '');

            $html = $this->render('index.php', ['categories' => $categories, 'options' => $options, 'products' => $products]);

            return new HtmlResponse($html, 200);

        } catch (\Throwable $e) {
            return new HtmlResponse(
                '<h1>Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>',
                500
            );
        }
    }
}
