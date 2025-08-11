<?php
declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use Raketa\BackendTestTask\Exception\CategoryNotFoundException;
use Raketa\BackendTestTask\Manager\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Psr\Log\LoggerInterface;

final readonly class ProductService
{
    /**
     * @param ProductRepository $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ProductRepository $productRepository,
        private LoggerInterface   $logger,
    )
    {
    }

    /**
     * @throws CategoryNotFoundException
     */
    public function getProductsByCategory(string $category): array
    {
        if (!$this->productRepository->categoryExists($category)) {
            $this->logger->error('Category not found.', [
                'category' => $category,
            ]);
            throw new CategoryNotFoundException("Category '{$category}' does not exist.");
        }

        return $this->productRepository->getByCategory($category);
    }
}