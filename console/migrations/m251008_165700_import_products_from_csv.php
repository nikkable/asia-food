<?php

use yii\db\Migration;
use repositories\Product\models\Product;
use repositories\Category\models\Category;

/**
 * Class m251008_165700_import_products_from_csv
 */
class m251008_165700_import_products_from_csv extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        echo "Миграция для импорта товаров из CSV файла.\n";
        echo "Для выполнения импорта используйте консольную команду:\n";
        echo "php yii import/products\n";
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251008_165700_import_products_from_csv не может быть отменена.\n";

        return false;
    }
}
