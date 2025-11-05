<?php

use yii\db\Migration;

/**
 * Добавляет поле external_id в таблицу order для связи с 1С
 */
class m251105_071900_add_external_id_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Добавляем поле external_id
        $this->addColumn('{{%order}}', 'external_id', $this->string(50)->null()->comment('ID заказа в 1С'));
        
        // Создаем индекс для быстрого поиска
        $this->createIndex('idx-order-external_id', '{{%order}}', 'external_id');
        
        // Добавляем поле exported_at для отслеживания времени экспорта
        $this->addColumn('{{%order}}', 'exported_at', $this->integer()->null()->comment('Время экспорта в 1С'));
        
        // Добавляем поле export_status для статуса экспорта
        $this->addColumn('{{%order}}', 'export_status', $this->tinyInteger()->defaultValue(0)->comment('Статус экспорта: 0-не экспортирован, 1-экспортирован, 2-ошибка'));
        
        // Создаем индекс для поиска по статусу экспорта
        $this->createIndex('idx-order-export_status', '{{%order}}', 'export_status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем индексы
        $this->dropIndex('idx-order-export_status', '{{%order}}');
        $this->dropIndex('idx-order-external_id', '{{%order}}');
        
        // Удаляем поля
        $this->dropColumn('{{%order}}', 'export_status');
        $this->dropColumn('{{%order}}', 'exported_at');
        $this->dropColumn('{{%order}}', 'external_id');
    }
}
