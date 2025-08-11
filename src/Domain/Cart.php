<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain;

final class Cart
{
    public function __construct(
        readonly private string $uuid,
        readonly private Customer $customer,
        readonly private string $paymentMethod,
        private array $items,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): void
    {
        foreach ($this->items as $existingitem) {
            if ($item->getProductUuid() === $existingitem->getProductUuid()){
                $existingitem->addQuantity($item->getQuantity());
                return;
            }
        }

        $this->items[] = $item;
    }
}
