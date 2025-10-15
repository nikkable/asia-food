<?php

namespace context\Payment;

use context\Payment\interfaces\PaymentServiceInterface;
use context\Payment\services\MockPaymentService;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Yii::$container->setDefinitions([
            PaymentServiceInterface::class => MockPaymentService::class,
        ]);
    }
}
