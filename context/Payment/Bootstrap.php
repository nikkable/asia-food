<?php

namespace context\Payment;

use context\Payment\interfaces\PaymentServiceInterface;
use context\Payment\services\MockPaymentService;
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
        $container->setSingleton(PaymentServiceInterface::class, MockPaymentService::class);
    }
}
