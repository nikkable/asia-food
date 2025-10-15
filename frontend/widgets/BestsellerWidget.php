<?php

namespace frontend\widgets;

use context\Favorite\interfaces\FavoriteServiceInterface;
use context\Product\interfaces\BestsellerServiceInterface;
use yii\base\Widget;

/**
 * Виджет для отображения хитов продаж
 */
class BestsellerWidget extends Widget
{
    public string $title = 'Хиты продаж';
    
    public string $subtitle = 'Самые популярные товары нашего магазина';
    
    public int $limit = 20;
    
    public function __construct(
        private readonly BestsellerServiceInterface $bestsellerService,
        private readonly FavoriteServiceInterface $favoriteService,
        array $config = []
    ) {
        parent::__construct($config);
    }
    
    public function run()
    {
        $bestsellers = $this->bestsellerService->getBestsellers($this->limit);
        
        return $this->render('bestseller-widget', [
            'bestsellers' => $bestsellers,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'favoriteService' => $this->favoriteService
        ]);
    }
}
