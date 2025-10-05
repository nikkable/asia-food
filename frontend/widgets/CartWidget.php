<?php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use context\Cart\interfaces\CartServiceInterface;

/**
 * Виджет корзины с кнопкой и модалкой для быстрого заказа
 */
class CartWidget extends Widget
{
    public function run()
    {
        $cartService = \Yii::$container->get(CartServiceInterface::class);
        $cart = $cartService->getCart();

        return $this->render('cart-widget', [
            'cart' => $cart,
        ]);
    }
}
