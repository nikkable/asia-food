<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Модель для формы быстрого заказа из корзины
 */
class QuickOrderForm extends Model
{
    public $customerName;
    public $customerPhone;
    public $customerEmail;
    public $deliveryAddress;
    public $orderComment;
    public $paymentMethod;
    
    // Способы оплаты
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_CARD = 'card';

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['customerName', 'customerPhone', 'paymentMethod'], 'required'],
            [['customerName'], 'string', 'max' => 100],
            [['customerPhone'], 'string', 'max' => 20],
            [['customerEmail'], 'email'],
            [['customerEmail'], 'string', 'max' => 100],
            [['deliveryAddress'], 'string', 'max' => 255],
            [['orderComment'], 'string', 'max' => 1000],
            ['paymentMethod', 'in', 'range' => [self::PAYMENT_METHOD_CASH, self::PAYMENT_METHOD_CARD]],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'customerName' => 'Имя',
            'customerPhone' => 'Телефон',
            'customerEmail' => 'Email',
            'deliveryAddress' => 'Адрес доставки',
            'orderComment' => 'Комментарий',
            'paymentMethod' => 'Способ оплаты',
        ];
    }
    
    /**
     * Список доступных способов оплаты
     * @return array
     */
    public function getPaymentMethodOptions()
    {
        return [
            self::PAYMENT_METHOD_CASH => 'Наличными при получении',
            self::PAYMENT_METHOD_CARD => 'Оплата картой онлайн',
        ];
    }
}
