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

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['customerName', 'customerPhone'], 'required'],
            [['customerName'], 'string', 'max' => 100],
            [['customerPhone'], 'string', 'max' => 20],
            [['customerEmail'], 'email'],
            [['customerEmail'], 'string', 'max' => 100],
            [['deliveryAddress'], 'string', 'max' => 255],
            [['orderComment'], 'string', 'max' => 1000],
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
        ];
    }
}
