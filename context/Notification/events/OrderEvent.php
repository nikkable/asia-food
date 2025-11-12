<?php

namespace context\Notification\events;

use yii\base\Event;
use repositories\Order\models\Order;

class OrderEvent extends Event
{
    public Order $order;
    public ?int $oldStatus = null;
    public ?int $newStatus = null;
}
