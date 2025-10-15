<?php

namespace frontend\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;

/**
 * Виджет поиска товаров с автодополнением
 */
class ProductSearchWidget extends Widget
{
    public string $placeholder = 'Поиск товаров...';
    
    public string $inputClass = '';
    
    public string $containerClass = '';

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
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
