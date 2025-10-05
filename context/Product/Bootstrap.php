<?php

namespace context\Product;

use context\AbstractBootstrap;
use context\Product\interfaces\BestsellerServiceInterface;
use context\Product\services\BestsellerService;
use repositories\Product\interfaces\BestsellerRepositoryInterface;
use repositories\Product\BestsellerRepository;
use yii\base\BootstrapInterface;
use yii\di\Container;

/**
 * Bootstrap для домена Product
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        $container = \Yii::$container;
        
        $this->registerServices($container);
    }
    
    /**
     * Регистрация сервисов
     * 
     * @param Container $container
     */
    private function registerServices(Container $container)
    {
        // Регистрация репозитория хитов продаж
        $container->set(BestsellerRepositoryInterface::class, BestsellerRepository::class);
        
        // Регистрация сервиса хитов продаж
        $container->set(BestsellerServiceInterface::class, BestsellerService::class);
    }
}
