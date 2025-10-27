<?php

use yii\db\Migration;

/**
 * Создание таблицы платежей для интеграции с ЮKassa
 */
class m251027_174500_create_payment_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull()->comment('ID заказа'),
            'payment_id' => $this->string(50)->notNull()->unique()->comment('ID платежа в ЮKassa'),
            'status' => $this->string(50)->notNull()->comment('Статус платежа'),
            'amount' => $this->decimal(10, 2)->notNull()->comment('Сумма платежа'),
            'currency' => $this->string(3)->notNull()->defaultValue('RUB')->comment('Валюта'),
            'payment_method_type' => $this->string(50)->null()->comment('Тип платежного средства'),
            'payment_method_id' => $this->string(50)->null()->comment('ID платежного средства'),
            'description' => $this->string(500)->null()->comment('Описание платежа'),
            'metadata' => $this->json()->null()->comment('Метаданные платежа'),
            'confirmation_url' => $this->string(500)->null()->comment('URL для подтверждения'),
            'refund_id' => $this->string(50)->null()->comment('ID возврата'),
            'refund_amount' => $this->decimal(10, 2)->null()->comment('Сумма возврата'),
            'refund_status' => $this->string(50)->null()->comment('Статус возврата'),
            'webhook_data' => $this->json()->null()->comment('Данные последнего webhook'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'updated_at' => $this->integer()->notNull()->comment('Дата обновления'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        // Создаем индексы
        $this->createIndex('idx-payment-order_id', '{{%payment}}', 'order_id');
        $this->createIndex('idx-payment-payment_id', '{{%payment}}', 'payment_id');
        $this->createIndex('idx-payment-status', '{{%payment}}', 'status');
        $this->createIndex('idx-payment-created_at', '{{%payment}}', 'created_at');

        // Добавляем внешний ключ на таблицу заказов
        $this->addForeignKey(
            'fk-payment-order_id',
            '{{%payment}}',
            'order_id',
            '{{%order}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        // Удаляем внешний ключ
        $this->dropForeignKey('fk-payment-order_id', '{{%payment}}');
        
        // Удаляем индексы
        $this->dropIndex('idx-payment-order_id', '{{%payment}}');
        $this->dropIndex('idx-payment-payment_id', '{{%payment}}');
        $this->dropIndex('idx-payment-status', '{{%payment}}');
        $this->dropIndex('idx-payment-created_at', '{{%payment}}');
        
        // Удаляем таблицу
        $this->dropTable('{{%payment}}');
    }
}
