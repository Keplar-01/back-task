<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Repository\ProductRepository;

readonly class CartView
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(' ', [
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    $cart->getCustomer()->getMiddleName(),
                ]),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
            'total' => 0,
            'items' => [],
        ];

        $cartItems = $cart->getItems();
        if (empty($cartItems)) {
            return $data;
        }

        $itemUuids = array_map(
            fn(CartItem $item): string => $item->getProductUuid(),
            $cartItems
        );

        $products = $this->productRepository->getByUuids($itemUuids);
        $productsByUuid = [];
        foreach ($products as $product) {
            $productsByUuid[$product->getUuid()] = $product;
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $product = $productsByUuid[$item->getProductUuid()] ?? null;
            if ($product === null) {
                continue;
            }

            $itemTotal = $item->getPrice() * $item->getQuantity();
            $total += $itemTotal;

            $data['items'][] = [
                'uuid' => $item->getUuid(),
                'price' => $item->getPrice(),
                'total' => $itemTotal,
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $product->getId(),
                    'uuid' => $product->getUuid(),
                    'name' => $product->getName(),
                    'thumbnail' => $product->getThumbnail(),
                    'price' => $product->getPrice(),
                ],
            ];
        }

        $data['total'] = $total;

        return $data;
    }
}
