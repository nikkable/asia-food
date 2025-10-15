<?php

namespace frontend\controllers;

use context\Seo\interfaces\SeoServiceInterface;
use repositories\Seo\models\SeoData;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\web\Controller;

/**
 * Базовый контроллер с SEO функциональностью
 */
abstract class BaseSeoController extends Controller
{
    protected $seoService;

    /**
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->seoService = Yii::$container->get(SeoServiceInterface::class);
    }

    /**
     * Устанавливает SEO данные для страницы
     */
    protected function setSeoData(SeoData $seoData): void
    {
        $this->seoService->setSeoData($seoData);
    }

    /**
     * Устанавливает SEO для главной страницы
     */
    protected function setHomeSeoData(): void
    {
        $this->setSeoData($this->seoService->getHomeSeoData());
    }

    /**
     * Устанавливает SEO для каталога
     */
    protected function setCatalogSeoData(): void
    {
        $this->setSeoData($this->seoService->getCatalogSeoData());
    }

    /**
     * Устанавливает SEO для категории
     */
    protected function setCategorySeoData(string $categoryName, string $categorySlug): void
    {
        $this->setSeoData($this->seoService->getCategorySeoData($categoryName, $categorySlug));
    }

    /**
     * Устанавливает SEO для товара
     */
    protected function setProductSeoData(string $productName, string $productSlug, ?string $categoryName = null): void
    {
        $this->setSeoData($this->seoService->getProductSeoData($productName, $productSlug, $categoryName));
    }

    /**
     * Устанавливает SEO для информационных страниц
     */
    protected function setPageSeoData(string $pageTitle, string $pageSlug): void
    {
        $this->setSeoData($this->seoService->getPageSeoData($pageTitle, $pageSlug));
    }

    /**
     * Устанавливает SEO для поиска
     */
    protected function setSearchSeoData(string $query): void
    {
        $this->setSeoData($this->seoService->getSearchSeoData($query));
    }
}
