<?php

namespace repositories;

use repositories\Category\CategoryRepository;
use repositories\Favorite\FavoriteRepository;
use repositories\Favorite\interfaces\FavoriteRepositoryInterface;
use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\OrderRepository;
use repositories\Payment\interfaces\PaymentRepositoryInterface;
use repositories\Payment\PaymentRepository;
use repositories\Product\BestsellerRepository;
use repositories\Product\interfaces\BestsellerRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use repositories\Commerce1C\Commerce1CSyncRepository;
use repositories\Product\ProductRepository;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Yii::$container->setDefinitions([
            CategoryRepositoryInterface::class => CategoryRepository::class,
            Commerce1CSyncRepositoryInterface::class => Commerce1CSyncRepository::class,
            FavoriteRepositoryInterface::class => FavoriteRepository::class,
            OrderRepositoryInterface::class => OrderRepository::class,
            PaymentRepositoryInterface::class => PaymentRepository::class,
            ProductRepositoryInterface::class => ProductRepository::class,
            BestsellerRepositoryInterface::class => BestsellerRepository::class,
        ]);
    }
}
