<?php

namespace context\Cart;

use context\Cart\interfaces\CartServiceInterface;
use context\Cart\services\CartService;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Yii::$container->setDefinitions([
            CartServiceInterface::class => CartService::class,
        ]);
    }
}
