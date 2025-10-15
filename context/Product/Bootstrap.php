<?php

namespace context\Product;

use context\Product\interfaces\ProductServiceInterface;
use context\Product\services\ProductService;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Yii::$container->setDefinitions([
            ProductServiceInterface::class => ProductService::class,
        ]);
    }
}
