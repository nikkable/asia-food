<?php

namespace context\Notification;

use Yii;
use yii\base\BootstrapInterface;
use context\Notification\services\NotificationService;
use context\Notification\events\OrderEvent;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->on(NotificationEvents::ORDER_CREATED, function (OrderEvent $event) {
            Yii::$container->get(NotificationService::class)->sendOrderCreated($event->order);
        });

        $app->on(NotificationEvents::ORDER_PAID, function (OrderEvent $event) {
            Yii::$container->get(NotificationService::class)->sendOrderPaid($event->order);
        });

        $app->on(NotificationEvents::ORDER_STATUS_CHANGED, function (OrderEvent $event) {
            Yii::$container->get(NotificationService::class)->sendOrderStatusChanged($event->order, $event->oldStatus, $event->newStatus);
        });
    }
}
