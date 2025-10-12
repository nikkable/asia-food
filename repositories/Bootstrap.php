<?php

namespace repositories;

use repositories\Favorite\FavoriteRepository;
use repositories\Favorite\interfaces\FavoriteRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use repositories\Commerce1C\Commerce1CSyncRepository;
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

        \Yii::$container->setSingleton(Commerce1CSyncRepositoryInterface::class, function (Container $container) {
            return new Commerce1CSyncRepository(
                $container->get(CategoryRepositoryInterface::class),
                $container->get(ProductRepositoryInterface::class)
            );
        });
    }
}
