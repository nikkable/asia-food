<?php

namespace context\Favorite;

use context\Favorite\interfaces\FavoriteServiceInterface;
use context\Favorite\services\FavoriteService;
use repositories\Favorite\FavoriteRepository;
use repositories\Favorite\interfaces\FavoriteRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;
use yii\base\BootstrapInterface;
use yii\di\Container;

/**
 * Bootstrap для домена Favorite
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        \Yii::$container->setSingleton(FavoriteServiceInterface::class, function (Container $container) {
            return new FavoriteService(
                $container->get(FavoriteRepositoryInterface::class),
                $container->get(ProductRepositoryInterface::class)
            );
        });
    }
}
