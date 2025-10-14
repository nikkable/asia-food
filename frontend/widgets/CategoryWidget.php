<?php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use repositories\Category\interfaces\CategoryRepositoryInterface;

/**
 * Виджет для отображения категорий на главной странице
 */
class CategoryWidget extends Widget
{
    /**
     * @var string Заголовок секции
     */
    public $title = 'Продукты для HoReCa: опт и розница с доставкой по Оренбургу и области';
    
    /**
     * @var string Подзаголовок секции
     */
    public $subtitle = 'Бесплатная доставка по городу при заказе от 5 000 рублей';
    
    /**
     * @var int Максимальное количество категорий для отображения
     */
    public $limit = 20;

    public function run()
    {
        $categoryRepository = \Yii::$container->get(CategoryRepositoryInterface::class);
        $categories = $categoryRepository->getRoot();
        
        // Ограничиваем количество категорий
        if ($this->limit > 0) {
            $categories = array_slice($categories, 0, $this->limit);
        }

        return $this->render('category-widget', [
            'categories' => $categories,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
        ]);
    }
}
