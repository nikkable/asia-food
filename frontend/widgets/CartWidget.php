<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class CartWidget extends Widget
{
    private $cartService;

    public function __construct(\context\Cart\interfaces\CartServiceInterface $cartService, $config = [])
    {
        $this->cartService = $cartService;
        parent::__construct($config);
    }

    public function run()
    {
        $cart = $this->cartService->getCart();
        return Html::a(
            'Корзина (' . $cart->getAmount() . ')',
            ['/cart/index'],
            ['class' => 'btn btn-link']
        );
    }
}
