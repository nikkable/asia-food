<?php

namespace frontend\widgets;

use yii\base\Widget;

/**
 * Виджет для карты
 */
class MapWidget extends Widget
{
    public string $title = 'Адрес для самовывоза';
    
    public function run()
    {
        return $this->render('map-widget', [
            'title' => $this->title,
        ]);
    }
}
