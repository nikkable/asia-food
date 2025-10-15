<?php

namespace frontend\widgets;

use yii\base\Widget;
use context\Cart\interfaces\CartServiceInterface;

/**
 * Виджет корзины с кнопкой и модалкой для быстрого заказа
 */
class CartWidget extends Widget
{
    public function __construct(
        private readonly CartServiceInterface $cartService,
        array $config = []
    ) {
        parent::__construct($config);
    }

    public function run()
    {
        $cart = $this->cartService->getCart();

        return $this->render('cart-widget', [
            'cart' => $cart,
        ]);
    }
}
