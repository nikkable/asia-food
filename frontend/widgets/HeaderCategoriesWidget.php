<?php

namespace frontend\widgets;

use yii\base\Widget;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use Yii;

/**
 * Виджет для отображения списка категорий в шапке сайта
 */
class HeaderCategoriesWidget extends Widget
{
    public int $limit = 20;

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        array $config = []
    )
    {
        parent::__construct($config);
    }

    public function run()
    {
        $categories = $this->categoryRepository->getRootWithChildren();
        
        if ($this->limit > 0) {
            $categories = array_slice($categories, 0, $this->limit);
        }

        $currentCategorySlug = $this->getCurrentCategorySlug();

        return $this->render('header-categories-widget', [
            'categories' => $categories,
            'currentCategorySlug' => $currentCategorySlug,
        ]);
    }

    private function getCurrentCategorySlug(): ?string
    {
        $route = Yii::$app->controller->route;
        $params = Yii::$app->request->get();

        if ($route === 'category/view' && isset($params['slug'])) {
            return $params['slug'];
        }

        return null;
    }
}
