<?php

namespace context\Delivery;

use yii\base\BootstrapInterface;
use context\Delivery\interfaces\DeliveryServiceInterface;
use context\Delivery\services\DeliveryService;
use Yii;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Yii::$container->set(DeliveryServiceInterface::class, DeliveryService::class);
    }
}
