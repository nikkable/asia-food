<?php

namespace frontend\widgets;

use yii\base\Widget;
use repositories\Category\interfaces\CategoryRepositoryInterface;

/**
 * Виджет для отображения категорий на главной странице
 */
class CategoryWidget extends Widget
{
    public string $title = 'Продукты для HoReCa: опт и розница с доставкой по Оренбургу и области';
    
    public string $subtitle = 'Бесплатная доставка по городу при заказе от 5 000 рублей';
    
    public int $limit = 20;

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        array $config = []
    ) {
        parent::__construct($config);
    }

    public function run()
    {
        $categories = $this->categoryRepository->getRoot();
        
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
