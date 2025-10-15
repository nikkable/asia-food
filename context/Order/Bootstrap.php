<?php

namespace context\Order;

use context\Order\interfaces\OrderServiceInterface;
use context\Order\services\OrderService;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Yii::$container->setDefinitions([
            OrderServiceInterface::class => OrderService::class,
        ]);
    }
}
