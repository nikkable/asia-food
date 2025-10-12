<?php

use yii\db\Migration;

/**
 * Handles adding external_id to table `{{%product}}`.
 */
class m251013_003000_add_external_id_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'external_id', $this->string(255)->unique()->null()->comment('Внешний ID (1C)'));
        
        // Создаем индекс для быстрого поиска
        $this->createIndex(
            'idx-product-external_id',
            '{{%product}}',
            'external_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-product-external_id', '{{%product}}');
        $this->dropColumn('{{%product}}', 'external_id');
    }
}
