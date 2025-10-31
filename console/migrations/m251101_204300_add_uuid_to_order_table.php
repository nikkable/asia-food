<?php

use yii\db\Migration;

/**
 * Handles adding uuid to table `{{%order}}`.
 */
class m251101_204300_add_uuid_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Добавляем поле uuid как nullable сначала
        $this->addColumn('{{%order}}', 'uuid', $this->string(36)->null()->after('id'));
        
        // Генерируем UUID для всех существующих заказов
        $orders = $this->db->createCommand('SELECT id FROM {{%order}}')->queryAll();
        foreach ($orders as $order) {
            $uuid = $this->generateUuid();
            $this->update('{{%order}}', ['uuid' => $uuid], ['id' => $order['id']]);
        }
        
        // Теперь делаем поле NOT NULL и UNIQUE
        $this->alterColumn('{{%order}}', 'uuid', $this->string(36)->notNull()->unique());
        
        // Создаем индекс для быстрого поиска
        $this->createIndex('idx-order-uuid', '{{%order}}', 'uuid');
    }
    
    /**
     * Генерирует UUID v4 с использованием Yii2 Security
     */
    private function generateUuid(): string
    {
        return Yii::$app->security->generateRandomString(36);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем индекс
        $this->dropIndex('idx-order-uuid', '{{%order}}');
        
        // Удаляем поле
        $this->dropColumn('{{%order}}', 'uuid');
    }
}
