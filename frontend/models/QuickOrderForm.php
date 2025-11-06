<?php

namespace frontend\models;

use common\models\User;
use Yii;
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
            [['customerName', 'customerPhone', 'paymentMethod'], 'required', 'message' => '{attribute} обязательно для заполнения.'],
            [['customerName'], 'string', 'max' => 100, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            [['customerPhone'], 'string', 'max' => 20, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            [['customerEmail'], 'email', 'message' => 'Неверный формат email адреса.'],
            [['customerEmail'], 'string', 'max' => 100, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            [['deliveryAddress'], 'string', 'max' => 255, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            [['orderComment'], 'string', 'max' => 1000, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            ['paymentMethod', 'in', 'range' => [self::PAYMENT_METHOD_CASH, self::PAYMENT_METHOD_CARD], 'message' => 'Выберите корректный способ оплаты.'],
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
    
    /**
     * Заполнить форму данными авторизованного пользователя
     */
    public function fillFromUser()
    {
        if (!Yii::$app->user->isGuest) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            
            // Заполняем поля только если они пустые
            if (empty($this->customerName) && !empty($user->full_name)) {
                $this->customerName = $user->full_name;
            }
            
            if (empty($this->customerPhone) && !empty($user->phone)) {
                $this->customerPhone = $user->phone;
            }
            
            if (empty($this->customerEmail) && !empty($user->email)) {
                $this->customerEmail = $user->email;
            }
            
            if (empty($this->deliveryAddress) && !empty($user->delivery_address)) {
                $this->deliveryAddress = $user->delivery_address;
            }
        }
    }
    
    /**
     * Проверить, заполнены ли основные поля из профиля пользователя
     * @return bool
     */
    public function hasUserData()
    {
        return !empty($this->customerName) || !empty($this->customerPhone) || !empty($this->deliveryAddress);
    }
}
