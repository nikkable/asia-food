<?php

namespace frontend\widgets;

use yii\base\Widget;

/**
 * Виджет первого экрана - баннеры
 */
class ScreenWidget extends Widget
{
    public function run()
    {
        return $this->render('screen-widget');
    }
}
