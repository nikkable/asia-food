<?php

namespace context\Delivery\services;

use context\Cart\models\Cart;
use context\Delivery\interfaces\DeliveryServiceInterface;

class DeliveryService implements DeliveryServiceInterface
{
    public function calculate(Cart $cart, string $method): array
    {
        $total = (float)$cart->getTotalCost();
        if ($method === 'pickup') {
            return [
                'title' => 'Самовывоз',
                'cost' => 0.0,
                'cost_text' => 'Бесплатно',
                'time_text' => 'В течение дня при заказе до 11:00',
                'area_text' => 'В пределах города. По области — индивидуально.'
            ];
        }
        $cost = $total >= 5000 ? 0.0 : 450.0;
        return [
            'title' => 'Доставка курьером',
            'cost' => $cost,
            'cost_text' => $cost > 0 ? '450 ₽ (бесплатно от 5 000 ₽)' : 'Бесплатно',
            'time_text' => 'В течение дня при заказе до 11:00',
            'area_text' => 'В пределах города. По области — индивидуально.'
        ];
    }
}
