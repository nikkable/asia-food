<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $full_name
 * @property string $phone
 * @property string $delivery_address
 * @property string $birth_date
 * @property integer $gender
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    
    const GENDER_NOT_SPECIFIED = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            
            // Профильные поля
            ['full_name', 'required', 'message' => '{attribute} обязательно для заполнения.'],
            [['full_name', 'phone', 'delivery_address'], 'string'],
            ['full_name', 'string', 'min' => 2, 'max' => 255, 'tooShort' => '{attribute} должно содержать минимум {min} символа.', 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            ['full_name', 'validateFullName'],
            ['phone', 'string', 'max' => 20, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
            ['phone', 'match', 'pattern' => '/^[\+]?[0-9\s\-\(\)]{10,20}$/', 'message' => 'Неверный формат телефона. Используйте формат: +7 (999) 123-45-67'],
            ['birth_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Неверный формат даты.'],
            ['gender', 'integer'],
            ['gender', 'in', 'range' => [self::GENDER_NOT_SPECIFIED, self::GENDER_MALE, self::GENDER_FEMALE], 'message' => 'Выберите корректное значение.'],
            ['gender', 'default', 'value' => self::GENDER_NOT_SPECIFIED],
            ['delivery_address', 'string', 'max' => 500, 'tooLong' => '{attribute} не может содержать более {max} символов.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'status' => 'Статус',
            'created_at' => 'Дата регистрации',
            'updated_at' => 'Дата обновления',
            'full_name' => 'Фамилия и имя',
            'phone' => 'Телефон',
            'delivery_address' => 'Адрес доставки',
            'birth_date' => 'Дата рождения',
            'gender' => 'Пол',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username or email
     *
     * @param string $usernameOrEmail
     * @return static|null
     */
    public static function findByUsernameOrEmail($usernameOrEmail)
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', ['username' => $usernameOrEmail], ['email' => $usernameOrEmail]])
            ->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * Получить отображаемое имя пользователя
     * @return string
     */
    public function getDisplayName()
    {
        return $this->full_name ?: $this->username;
    }
    
    /**
     * Получить список полов для выпадающего списка
     * @return array
     */
    public static function getGenderList()
    {
        return [
            self::GENDER_NOT_SPECIFIED => 'Не указан',
            self::GENDER_MALE => 'Мужской',
            self::GENDER_FEMALE => 'Женский',
        ];
    }
    
    /**
     * Получить текстовое представление пола
     * @return string
     */
    public function getGenderText()
    {
        $genders = self::getGenderList();
        return $genders[$this->gender] ?? 'Не указан';
    }
    
    /**
     * Проверить, заполнен ли профиль пользователя
     * @return bool
     */
    public function isProfileComplete()
    {
        return !empty($this->full_name) && !empty($this->phone);
    }

    public function validateFullName($attribute, $params = [])
    {
        $value = trim((string)$this->$attribute);
        // Должно быть минимум два слова, разделённых пробелом
        if ($value === '' || !preg_match('/^\S+\s+\S+/u', $value)) {
            $this->addError($attribute, 'Введите, пожалуйста, фамилию и имя через пробел.');
        }
    }
}
