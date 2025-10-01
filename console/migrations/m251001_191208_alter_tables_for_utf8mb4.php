<?php

use yii\db\Migration;

class m251001_191208_alter_tables_for_utf8mb4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE {{%category}} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->execute('ALTER TABLE {{%product}} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251001_191208_alter_tables_for_utf8mb4 can be reverted manually if needed.\n";
    }
}
