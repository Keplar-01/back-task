<?php
declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Manager\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Ramsey\Uuid\Uuid;

final readonly class CartService
{
    /**
     * @param CartManager $cartManager
     * @param ProductRepository $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private CartManager       $cartManager,
        private ProductRepository $productRepository,
        private LoggerInterface   $logger,
    )
    {
    }

    /**
     * @return Cart|null
     */
    public function getCurrentCart(): ?Cart
    {
        return $this->cartManager->getCart();
    }

    /**
     * @param string $productUuid
     * @param int $quantity
     * @return Cart|null
     */
    public function addProductToCart(string $productUuid, int $quantity): ?Cart
    {
        if ($quantity <= 0) {
            $this->logger->warning('Invalid quantity for product add to cart.', [
                'product_uuid' => $productUuid,
                'quantity' => $quantity,
            ]);

            return null;
        }

        $product = $this->productRepository->getByUuid($productUuid);
        if ($product === null) {
            $this->logger->error('Product not found for add to cart.', [
                'product_uuid' => $productUuid,
            ]);

            return null;
        }

        $cart = $this->getOrCreateCart();

        $cartItem = new CartItem(
            Uuid::uuid4()->toString(),
            $product->getUuid(),
            $product->getPrice(),
            $quantity
        );
        $cart->addItem($cartItem);
        $this->cartManager->saveCart($cart);

        return $cart;
    }

    /**
     * @return Cart
     */
    private function getOrCreateCart(): Cart
    {
        $cart = $this->cartManager->getCart();

        if ($cart === null) {
            $this->logger->info('No existing cart found for session, creating a new one.', [
                'session_id' => session_id(),
            ]);
            $customer = new Customer(1, 'test', 'test', '', 'test@test.ru');
            $cart = new Cart(
                Uuid::uuid4()->toString(),
                $customer,
                'cash', // Метод оплаты по умолчанию
                []
            );
        }

        return $cart;
    }
}