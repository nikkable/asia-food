<?php

namespace context\Commerce1C\generators;

use repositories\Order\models\Order;
use repositories\Order\models\OrderItem;
use DOMDocument;
use DOMElement;
use Yii;

class OrderXmlGenerator
{
    private DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
    }

    /**
     * Генерирует XML с заказами в формате CommerceML
     */
    public function generate(array $orders): string
    {
        $root = $this->dom->createElement('КоммерческаяИнформация');
        $root->setAttribute('ВерсияСхемы', '2.05');
        $root->setAttribute('ДатаФормирования', date('Y-m-d\TH:i:s'));
        $this->dom->appendChild($root);

//        $documentsElement = $this->dom->createElement('Документы');
//        $root->appendChild($documentsElement);

        foreach ($orders as $order) {
            $this->addOrderToXml($root, $order);
        }

        return $this->dom->saveXML();
    }

    /**
     * Добавляет заказ в XML
     */
    private function addOrderToXml(DOMElement $parent, Order $order): void
    {
        $documentElement = $this->dom->createElement('Документ');
        $parent->appendChild($documentElement);

        // Основная информация о заказе
        $this->addElement($documentElement, 'Ид', $order->uuid);
        $this->addElement($documentElement, 'Номер', (string)$order->id);
        $this->addElement($documentElement, 'Дата', date('Y-m-d', $order->created_at));
        $this->addElement($documentElement, 'ХозОперация', 'Заказ товара');
        $this->addElement($documentElement, 'Роль', 'Продавец');
        $this->addElement($documentElement, 'Валюта', 'руб');
        $this->addElement($documentElement, 'Курс', '1');
        $this->addElement($documentElement, 'Сумма', number_format($order->total_cost, 2, '.', ''));

        // Статус заказа
        $this->addElement($documentElement, 'Статус', $this->getOrderStatusText($order->status));
        
        // Способ оплаты
        $this->addElement($documentElement, 'СпособОплаты', $this->getPaymentMethodText($order->payment_method));
        
        // Статус оплаты
        $this->addElement($documentElement, 'СтатусОплаты', $this->getPaymentStatusText($order->payment_status));

        // Контрагент (покупатель)
        $this->addContragent($documentElement, $order);

        // Товары заказа
        $this->addOrderItems($documentElement, $order);

        // Дополнительная информация
        if (!empty($order->note)) {
            $this->addElement($documentElement, 'Комментарий', $order->note);
        }

        // Реквизиты заказа
        $this->addOrderRequisites($documentElement, $order);
    }

    /**
     * Добавляет информацию о контрагенте
     */
    private function addContragent(DOMElement $parent, Order $order): void
    {
        $contragentsElement = $this->dom->createElement('Контрагенты');
        $parent->appendChild($contragentsElement);

        $contragentElement = $this->dom->createElement('Контрагент');
        $contragentsElement->appendChild($contragentElement);

        $this->addElement($contragentElement, 'Ид', 'customer_' . $order->id);
        $this->addElement($contragentElement, 'Наименование', $order->customer_name);
        $this->addElement($contragentElement, 'ПолноеНаименование', $order->customer_name);
        $this->addElement($contragentElement, 'Роль', 'Покупатель');

        // Контактная информация
        $contactsElement = $this->dom->createElement('Контакты');
        $contragentElement->appendChild($contactsElement);

        // Телефон
        if (!empty($order->customer_phone)) {
            $phoneElement = $this->dom->createElement('Контакт');
            $contactsElement->appendChild($phoneElement);
            $this->addElement($phoneElement, 'Тип', 'Телефон');
            $this->addElement($phoneElement, 'Значение', $order->customer_phone);
        }

        // Email
        if (!empty($order->customer_email)) {
            $emailElement = $this->dom->createElement('Контакт');
            $contactsElement->appendChild($emailElement);
            $this->addElement($emailElement, 'Тип', 'Почта');
            $this->addElement($emailElement, 'Значение', $order->customer_email);
        }
    }

    /**
     * Добавляет товары заказа
     */
    private function addOrderItems(DOMElement $parent, Order $order): void
    {
        $productsElement = $this->dom->createElement('Товары');
        $parent->appendChild($productsElement);

        foreach ($order->orderItems as $item) {
            $productElement = $this->dom->createElement('Товар');
            $productsElement->appendChild($productElement);

            $this->addElement($productElement, 'Ид', $item->product->external_id ?? 'product_' . $item->product_id);
            $this->addElement($productElement, 'Наименование', $item->product_name);
            $this->addElement($productElement, 'БазоваяЕдиница', 'шт');
            $this->addElement($productElement, 'Количество', (string)$item->quantity);
            $this->addElement($productElement, 'Цена', number_format($item->price, 2, '.', ''));
            $this->addElement($productElement, 'Сумма', number_format($item->cost, 2, '.', ''));

            // Если есть НДС
            $this->addElement($productElement, 'СтавкаНДС', 'Без НДС');
            $this->addElement($productElement, 'СуммаНДС', '0.00');
        }
    }

    /**
     * Добавляет реквизиты заказа
     */
    private function addOrderRequisites(DOMElement $parent, Order $order): void
    {
        $requisitesElement = $this->dom->createElement('ЗначенияРеквизитов');
        $parent->appendChild($requisitesElement);

        // UUID заказа
        $this->addRequisite($requisitesElement, 'UUID_Заказа', $order->uuid);

        // ID пользователя
        if ($order->user_id) {
            $this->addRequisite($requisitesElement, 'ID_Пользователя', (string)$order->user_id);
        }

        // Дата создания
        $this->addRequisite($requisitesElement, 'ДатаСоздания', date('Y-m-d H:i:s', $order->created_at));

        // Дата обновления
        $this->addRequisite($requisitesElement, 'ДатаОбновления', date('Y-m-d H:i:s', $order->updated_at));

        // ID транзакции оплаты
        if (!empty($order->payment_transaction_id)) {
            $this->addRequisite($requisitesElement, 'ID_Транзакции', $order->payment_transaction_id);
        }
    }

    /**
     * Добавляет реквизит
     */
    private function addRequisite(DOMElement $parent, string $name, string $value): void
    {
        $requisiteElement = $this->dom->createElement('ЗначениеРеквизита');
        $parent->appendChild($requisiteElement);

        $this->addElement($requisiteElement, 'Наименование', $name);
        $this->addElement($requisiteElement, 'Значение', $value);
    }

    /**
     * Добавляет элемент с текстом
     */
    private function addElement(DOMElement $parent, string $name, string $value): DOMElement
    {
        $element = $this->dom->createElement($name);
        $element->appendChild($this->dom->createTextNode($value));
        $parent->appendChild($element);
        return $element;
    }

    /**
     * Получает текстовое представление статуса заказа
     */
    private function getOrderStatusText(int $status): string
    {
        return match ($status) {
            Order::STATUS_NEW => 'Новый',
            Order::STATUS_PROCESSING => 'В обработке',
            Order::STATUS_COMPLETED => 'Выполнен',
            Order::STATUS_CANCELLED => 'Отменен',
            default => 'Неизвестно'
        };
    }

    /**
     * Получает текстовое представление способа оплаты
     */
    private function getPaymentMethodText(string $method): string
    {
        return match ($method) {
            Order::PAYMENT_METHOD_CASH => 'Наличными',
            Order::PAYMENT_METHOD_CARD => 'Картой онлайн',
            default => 'Неизвестно'
        };
    }

    /**
     * Получает текстовое представление статуса оплаты
     */
    private function getPaymentStatusText(int $status): string
    {
        return match ($status) {
            Order::PAYMENT_STATUS_PENDING => 'Ожидает оплаты',
            Order::PAYMENT_STATUS_PAID => 'Оплачен',
            Order::PAYMENT_STATUS_FAILED => 'Ошибка оплаты',
            default => 'Неизвестно'
        };
    }
}
