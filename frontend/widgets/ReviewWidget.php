<?php

namespace frontend\widgets;

use yii\base\Widget;

/**
 * Виджет для отображения отзывов
 */
class ReviewWidget extends Widget
{
    public string $title = 'Отзывы';
    
    public function run()
    {
        return $this->render('review-widget', [
            'title' => $this->title,
        ]);
    }
}
