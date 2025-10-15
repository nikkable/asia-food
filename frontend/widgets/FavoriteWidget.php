<?php

namespace frontend\widgets;

use yii\base\Widget;
use context\Favorite\interfaces\FavoriteServiceInterface;

/**
 * Виджет избранного с кнопкой и модалкой для просмотра
 */
class FavoriteWidget extends Widget
{
    public function __construct(
        private readonly FavoriteServiceInterface $favoriteService,
        array $config = []
    )
    {
        parent::__construct($config);
    }

    public function run()
    {
        $favorites = $this->favoriteService->getFavorites();
        
        return $this->render('favorite-widget', [
            'favorites' => $favorites,
        ]);
    }
}
