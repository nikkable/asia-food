<?php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use repositories\Category\interfaces\CategoryRepositoryInterface;

/**
 * Виджет для отображения списка категорий в шапке сайта
 */
class HeaderCategoriesWidget extends Widget
{
    /**
     * @var int Максимальное количество категорий для отображения в меню
     */
    public $limit = 15;

    public function run()
    {
        $categoryRepository = \Yii::$container->get(CategoryRepositoryInterface::class);
        $categories = $categoryRepository->getRoot();
        
        // Ограничиваем количество категорий для меню
        if ($this->limit > 0) {
            $categories = array_slice($categories, 0, $this->limit);
        }

        return $this->render('header-categories-widget', [
            'categories' => $categories,
        ]);
    }
}
