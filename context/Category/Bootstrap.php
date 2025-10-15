<?php

namespace context\Category;

use context\Category\interfaces\CategoryServiceInterface;
use context\Category\services\CategoryService;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Yii::$container->setDefinitions([
            CategoryServiceInterface::class => CategoryService::class,
        ]);
    }
}
