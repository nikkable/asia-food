<?php

namespace frontend\widgets;

use context\Product\interfaces\BestsellerServiceInterface;
use yii\base\Widget;

/**
 * Виджет для отображения хитов продаж
 */
class BestsellerWidget extends Widget
{
    /**
     * @var string Заголовок блока
     */
    public $title = 'Хиты продаж';
    
    /**
     * @var string Подзаголовок блока
     */
    public $subtitle = 'Самые популярные товары нашего магазина';
    
    /**
     * @var int Количество товаров для отображения
     */
    public $limit = 20;
    
    /**
     * @var BestsellerServiceInterface
     */
    private $bestsellerService;
    
    /**
     * @param BestsellerServiceInterface $bestsellerService
     * @param array $config
     */
    public function __construct(
        BestsellerServiceInterface $bestsellerService,
        $config = []
    ) {
        $this->bestsellerService = $bestsellerService;
        parent::__construct($config);
    }
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $bestsellers = $this->bestsellerService->getBestsellers($this->limit);
        
        return $this->render('bestseller-widget', [
            'bestsellers' => $bestsellers,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
        ]);
    }
}
