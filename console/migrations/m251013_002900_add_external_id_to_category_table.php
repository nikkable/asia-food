<?php

use yii\db\Migration;

/**
 * Handles adding external_id to table `{{%category}}`.
 */
class m251013_002900_add_external_id_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%category}}', 'external_id', $this->string(255)->unique()->null()->comment('Внешний ID (1C)'));
        
        // Создаем индекс для быстрого поиска
        $this->createIndex(
            'idx-category-external_id',
            '{{%category}}',
            'external_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-category-external_id', '{{%category}}');
        $this->dropColumn('{{%category}}', 'external_id');
    }
}
