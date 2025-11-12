<?php

namespace context\Notification\services;

use repositories\Order\models\Order;
use Yii;

class NotificationService
{
    public function sendOrderCreated(Order $order): void
    {
        $this->sendToCustomer($order, 'Ваш заказ принят', $this->buildCustomerCreatedBody($order));
        $this->sendToAdmin('Новый заказ #' . $order->id, $this->buildAdminCreatedBody($order));
    }

    public function sendOrderPaid(Order $order): void
    {
        $this->sendToCustomer($order, 'Ваш заказ оплачен', $this->buildCustomerPaidBody($order));
        $this->sendToAdmin('Заказ #' . $order->id . ' оплачен', $this->buildAdminPaidBody($order));
    }

    public function sendOrderStatusChanged(Order $order, ?int $oldStatus, ?int $newStatus): void
    {
        $subject = 'Статус заказа #' . $order->id . ' изменен';
        $this->sendToCustomer($order, $subject, $this->buildCustomerStatusBody($order, $oldStatus, $newStatus));
        $this->sendToAdmin($subject, $this->buildAdminStatusBody($order, $oldStatus, $newStatus));
    }

    private function sendToCustomer(Order $order, string $subject, string $text): void
    {
        $to = $order->customer_email;
        if (!$to || $to === 'no-email@example.com') {
            return;
        }
        Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($to)
            ->setSubject($subject)
            ->setTextBody($text)
            ->send();
    }

    private function sendToAdmin(string $subject, string $text): void
    {
        $admin = Yii::$app->params['adminEmail'] ?? null;
        if (!$admin) {
            return;
        }
        Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($admin)
            ->setSubject($subject)
            ->setTextBody($text)
            ->send();
    }

    private function buildCustomerCreatedBody(Order $order): string
    {
        $url = Yii::$app->urlManager->createAbsoluteUrl(['/order/view', 'uuid' => $order->uuid]);
        return "Ваш заказ №{$order->id} принят.\nСумма: {$order->total_cost}\nСтатус: " . Order::getStatusInfo($order->status)['text'] . "\nСсылка: {$url}";
    }

    private function buildAdminCreatedBody(Order $order): string
    {
        return "Новый заказ №{$order->id}. Сумма: {$order->total_cost}. Клиент: {$order->customer_name}, {$order->customer_phone}, {$order->customer_email}.";
    }

    private function buildCustomerPaidBody(Order $order): string
    {
        $url = Yii::$app->urlManager->createAbsoluteUrl(['/order/view', 'uuid' => $order->uuid]);
        return "Оплата заказа №{$order->id} получена.\nСумма: {$order->total_cost}\nСтатус: Оплачен\nСсылка: {$url}";
    }

    private function buildAdminPaidBody(Order $order): string
    {
        return "Заказ №{$order->id} оплачен. Сумма: {$order->total_cost}. Клиент: {$order->customer_name}, {$order->customer_phone}.";
    }

    private function buildCustomerStatusBody(Order $order, ?int $old, ?int $new): string
    {
        $o = $old !== null ? Order::getStatusInfo($old)['text'] : '—';
        $n = $new !== null ? Order::getStatusInfo($new)['text'] : '—';
        $url = Yii::$app->urlManager->createAbsoluteUrl(['/order/view', 'uuid' => $order->uuid]);
        return "Статус заказа №{$order->id} изменен: {$o} → {$n}.\nСсылка: {$url}";
    }

    private function buildAdminStatusBody(Order $order, ?int $old, ?int $new): string
    {
        $o = $old !== null ? Order::getStatusInfo($old)['text'] : '—';
        $n = $new !== null ? Order::getStatusInfo($new)['text'] : '—';
        return "Статус заказа №{$order->id} изменен: {$o} → {$n}. Клиент: {$order->customer_name}, {$order->customer_phone}.";
    }
}
