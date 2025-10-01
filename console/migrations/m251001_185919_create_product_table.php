<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m251001_185919_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'image' => $this->string(),
            'price' => $this->decimal(10, 2)->notNull(),
            'price_discount' => $this->decimal(10, 2),
            'quantity' => $this->integer()->notNull()->defaultValue(0),
            'article' => $this->string()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-product-category_id',
            '{{%product}}',
            'category_id'
        );

        // Внешний ключ будет добавлен в миграции для категорий
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-product-category_id', '{{%product}}');
        $this->dropTable('{{%product}}');
    }
}
