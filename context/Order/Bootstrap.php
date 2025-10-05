<?php

namespace context\Order;

use context\Order\interfaces\OrderServiceInterface;
use context\Order\services\OrderService;
use yii\base\BootstrapInterface;
use yii\di\Container;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;
        
        $this->registerServices($container);
    }
    
    private function registerServices(Container $container): void
    {
        $container->setSingleton(OrderServiceInterface::class, OrderService::class);
    }
}
