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


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
