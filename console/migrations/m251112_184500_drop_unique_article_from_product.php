<?php

use yii\db\Migration;

class m251112_184500_drop_unique_article_from_product extends Migration
{
    public function safeUp()
    {
        $table = '{{%product}}';
        $db = $this->db;

        if ($db->driverName === 'mysql') {
            $quoted = $db->quoteTableName($table);
            $rows = $db->createCommand("SHOW INDEX FROM {$quoted} WHERE Column_name='article' AND Non_unique=0")->queryAll();
            foreach ($rows as $row) {
                $keyName = $row['Key_name'] ?? null;
                if ($keyName) {
                    $this->dropIndex($keyName, $table);
                }
            }
        } else {
            try {
                $this->dropIndex('article', $table);
            } catch (\Throwable $e) {
            }
            try {
                $this->dropIndex('idx-unique-product-article', $table);
            } catch (\Throwable $e) {
            }
        }
    }

    public function safeDown()
    {
        $this->createIndex('idx-unique-product-article', '{{%product}}', 'article', true);
    }
}
