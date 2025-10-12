<?php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;

/**
 * Виджет поиска товаров с автодополнением
 */
class ProductSearchWidget extends Widget
{
    /**
     * @var string $placeholder Placeholder для поля поиска
     */
    public string $placeholder = 'Поиск товаров...';
    
    /**
     * @var string $inputClass CSS класс для input поля
     */
    public string $inputClass = '';
    
    /**
     * @var string $containerClass CSS класс для контейнера
     */
    public string $containerClass = '';

    public function init()
    {
        parent::init();
        
        $this->view->registerJsFile('/js/product-search.js', ['position' => View::POS_END]);
    }

    public function run()
    {
        return $this->render('product-search', [
            'placeholder' => $this->placeholder,
            'inputClass' => $this->inputClass,
            'containerClass' => $this->containerClass,
            'searchUrl' => Url::to(['/search/index'])
        ]);
    }
}
