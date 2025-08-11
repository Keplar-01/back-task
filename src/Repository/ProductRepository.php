<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Raketa\BackendTestTask\Repository\Entity\Product;

class ProductRepository
{

    /**
     * @param Connection $connection
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly Connection      $connection,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * @param string $uuid
     * @return Product|null
     */
    public function getByUuid(string $uuid): ?Product
    {
        $row = $this->connection->fetchAssociative(
            "SELECT * FROM products WHERE uuid = ?",
            [$uuid]
        );

        if (empty($row)) {
            return null;
        }

        return $this->make($row);
    }

    /**
     * @param string $category
     * @return int[]
     */
    public function getByCategory(string $category): array
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT * FROM products WHERE is_active = 1 AND category = ?",
            [$category]
        );

        return array_map(
            [$this, 'make'],
            $rows
        );
    }

    /**
     * @param array $uuids
     * @return Product[]
     */
    public function getByUuids(array $uuids): array
    {
        if (empty($uuids)) {
            return [];
        }

        $rows = $this->connection->fetchAllAssociative(
            "SELECT * FROM products WHERE uuid IN (?)",
            [$uuids],
            ['array']
        );

        return array_map(
            [$this, 'make'],
            $rows
        );
    }

    /**
     * @param array $row
     * @return Product
     */
    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }

    /**
     * @param string $category
     * @return bool
     */
    public function categoryExists(string $category): bool
    {
        $isExist = $this->connection->fetchOne(
            "SELECT 1 FROM products WHERE category = ? LIMIT 1",
            [$category]
        );

        return $isExist !== false;
    }
}
