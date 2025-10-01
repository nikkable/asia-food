<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m251001_190432_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'name' => $this->string()->notNull(),
            'slug' => $this->string()->notNull()->unique(),
            'description' => $this->text(),
            'image' => $this->string(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Creates index for column `parent_id`
        $this->createIndex(
            'idx-category-parent_id',
            '{{%category}}',
            'parent_id'
        );

        // Adds foreign key for table `{{%category}}`
        $this->addForeignKey(
            'fk-category-parent_id',
            '{{%category}}',
            'parent_id',
            '{{%category}}',
            'id',
            'CASCADE'
        );

        // Adds foreign key for table `product`
        $this->addForeignKey(
            'fk-product-category_id',
            '{{%product}}',
            'category_id',
            '{{%category}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product-category_id', '{{%product}}');
        $this->dropForeignKey('fk-category-parent_id', '{{%category}}');
        $this->dropIndex('idx-category-parent_id', '{{%category}}');
        $this->dropTable('{{%category}}');
    }
}
