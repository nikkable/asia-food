<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_item}}`.
 */
class m251001_194508_create_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order_item}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer(),
            'product_name' => $this->string()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'quantity' => $this->integer()->notNull(),
            'cost' => $this->decimal(10, 2)->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-order_item-order_id', '{{%order_item}}', 'order_id', '{{%order}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-order_item-product_id', '{{%order_item}}', 'product_id', '{{%product}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order_item-order_id', '{{%order_item}}');
        $this->dropForeignKey('fk-order_item-product_id', '{{%order_item}}');
        $this->dropTable('{{%order_item}}');
    }
}
