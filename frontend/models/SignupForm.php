<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $full_name;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['full_name', 'trim'],
            ['full_name', 'required', 'message' => '{attribute} обязательно для заполнения.'],
            ['full_name', 'string', 'min' => 2, 'max' => 255, 'tooShort' => '{attribute} должно содержать минимум {min} символа.', 'tooLong' => '{attribute} не может содержать более {max} символов.'],

            ['username', 'trim'],
            ['username', 'required', 'message' => '{attribute} обязательно для заполнения.'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Это имя пользователя уже занято.'],
            ['username', 'string', 'min' => 2, 'max' => 255, 'tooShort' => '{attribute} должно содержать минимум {min} символа.', 'tooLong' => '{attribute} не может содержать более {max} символов.'],

            ['email', 'trim'],
            ['email', 'required', 'message' => '{attribute} обязательно для заполнения.'],
            ['email', 'email', 'message' => 'Неверный формат email адреса.'],
            ['email', 'string', 'max' => 255, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот email адрес уже зарегистрирован.'],

            ['password', 'required', 'message' => '{attribute} обязательно для заполнения.'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'], 'tooShort' => '{attribute} должен содержать минимум {min} символов.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'full_name' => 'Фамилия и имя',
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->full_name = $this->full_name;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
