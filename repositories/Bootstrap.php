<?php

namespace repositories;

use repositories\Favorite\FavoriteRepository;
use repositories\Favorite\interfaces\FavoriteRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;
use yii\base\BootstrapInterface;
use yii\di\Container;

/**
 * Bootstrap для регистрации репозиториев
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        \Yii::$container->setSingleton(FavoriteRepositoryInterface::class, function (Container $container) {
            return new FavoriteRepository(
                $container->get(ProductRepositoryInterface::class)
            );
        });
    }
}
