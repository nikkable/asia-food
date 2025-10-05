<?php

use yii\db\Migration;

/**
 * Добавляет поле способа оплаты в таблицу заказов
 */
class m251005_182500_add_payment_method_to_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'payment_method', $this->string()->notNull()->defaultValue('cash'));
        $this->addColumn('{{%order}}', 'payment_status', $this->smallInteger()->notNull()->defaultValue(0));
        $this->addColumn('{{%order}}', 'payment_transaction_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'payment_transaction_id');
        $this->dropColumn('{{%order}}', 'payment_status');
        $this->dropColumn('{{%order}}', 'payment_method');
    }
}
