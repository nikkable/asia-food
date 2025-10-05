<?php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use context\Favorite\interfaces\FavoriteServiceInterface;

/**
 * Виджет избранного с кнопкой и модалкой для просмотра
 */
class FavoriteWidget extends Widget
{
    public function run()
    {
        $favoriteService = \Yii::$container->get(FavoriteServiceInterface::class);
        $favorites = $favoriteService->getFavorites();
        
        return $this->render('favorite-widget', [
            'favorites' => $favorites,
        ]);
    }
}
