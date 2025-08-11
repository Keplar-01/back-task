<?php
declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Infrastructure\Http\JsonResponse;
use Raketa\BackendTestTask\Service\CartService;
use Raketa\BackendTestTask\View\CartView;
use Ramsey\Uuid\Uuid;

readonly class AddToCartController
{
    public function __construct(
        private CartService $cartService,
        private CartView    $cartView
    )
    {
    }

    public function add(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
        $cart = $this->cartService->addProductToCart($rawRequest['productUuid'], $rawRequest['quantity']);
        $response = new JsonResponse();

        if ($cart === null) {
            return $response->getBody()->write(
                json_encode(
                    ['message' => 'Failed to add product to cart'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                ))
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        $response->getBody()->write(
            json_encode(
                [
                    'status' => 'success',
                    'cart' => $this->cartView->toArray($cart)
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
