<?php

namespace frontend\models;

use yii\base\Model;

class OrderForm extends Model
{
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $note;

    public function rules()
    {
        return [
            [['customer_name', 'customer_email', 'customer_phone'], 'required'],
            [['customer_name', 'customer_email', 'customer_phone'], 'string', 'max' => 255],
            [['customer_email'], 'email'],
            [['note'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_name' => 'Ваше имя',
            'customer_email' => 'Email',
            'customer_phone' => 'Телефон',
            'note' => 'Примечание к заказу',
        ];
    }
}
