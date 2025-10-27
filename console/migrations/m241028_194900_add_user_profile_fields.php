<?php

use yii\db\Migration;

/**
 * Добавление полей профиля пользователя
 */
class m241028_194900_add_user_profile_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'full_name', $this->string(255)->null()->comment('Полное имя пользователя'));
        $this->addColumn('{{%user}}', 'phone', $this->string(20)->null()->comment('Телефон пользователя'));
        $this->addColumn('{{%user}}', 'delivery_address', $this->text()->null()->comment('Адрес доставки по умолчанию'));
        $this->addColumn('{{%user}}', 'birth_date', $this->date()->null()->comment('Дата рождения'));
        $this->addColumn('{{%user}}', 'gender', $this->tinyInteger(1)->null()->comment('Пол: 0-не указан, 1-мужской, 2-женский'));
        
        // Создаем индекс для телефона для быстрого поиска
        $this->createIndex('idx-user-phone', '{{%user}}', 'phone');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-phone', '{{%user}}');
        
        $this->dropColumn('{{%user}}', 'full_name');
        $this->dropColumn('{{%user}}', 'phone');
        $this->dropColumn('{{%user}}', 'delivery_address');
        $this->dropColumn('{{%user}}', 'birth_date');
        $this->dropColumn('{{%user}}', 'gender');
    }
}
