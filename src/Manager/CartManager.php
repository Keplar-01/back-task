<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Manager;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\Redis\Connector;
use Raketa\BackendTestTask\Infrastructure\Redis\ConnectorException;

readonly class CartManager
{

    public function __construct(
        private Connector       $connector,
        private LoggerInterface $logger,
    )
    {}


    /**
     * @inheritdoc
     */
    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set(session_id(), $cart);
        } catch (ConnectorException $ex) {
            $this->logger->error('Failed to save cart to Redis.', [
                'cart_uuid' => $cart->getUuid(),
                'session_id' => session_id(),
                'error' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * @return ?Cart
     */
    public function getCart(): ?Cart
    {
        try {
            $cart = $this->connector->get(session_id());

            return $cart !== null ? $cart : null;
        } catch (Exception $ex) {
            $this->logger->error('Failed to get cart from Redis.', [
                'session_id' => session_id(),
                'error' => $ex->getMessage(),
            ]);
        }

        return null;
    }
}
