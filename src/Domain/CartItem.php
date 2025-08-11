<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain;

final class CartItem
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $productUuid,
        public readonly float $price,
        public int $quantity,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getProductUuid(): string
    {
        return $this->productUuid;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function addQuantity(int $quantity): void
    {
        $this->quantity += $quantity;
    }
}
