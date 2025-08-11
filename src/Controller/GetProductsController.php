<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Infrastructure\Http\JsonResponse;
use Raketa\BackendTestTask\Service\ProductService;
use Raketa\BackendTestTask\View\ProductsView;

readonly class GetProductsController
{
    public function __construct(
        private ProductsView $productsView,
        private ProductService $productService
    ) {
    }

    public function get(RequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse();
        $queryParams = $request->getQueryParams();
        $category = $queryParams['category'] ?? null;

        if ($category === null || $category === '') {
            $response->getBody()->write(
                json_encode(
                    ['message' => 'Category parameter is required'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        try {
            $products = $this->productService->getProductsByCategory($category);
            $productsData = $this->productsView->toArray($products);
            $response->getBody()->write(json_encode(
                $productsData,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['message' => $ex->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus(404);
        }
    }
}
