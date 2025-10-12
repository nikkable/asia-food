<?php

namespace context\Seo\interfaces;

use repositories\Seo\models\SeoData;

interface SeoServiceInterface
{
    /**
     * Устанавливает SEO теги для текущей страницы
     */
    public function setSeoData(SeoData $seoData): void;

    /**
     * Генерирует SEO данные для главной страницы
     */
    public function getHomeSeoData(): SeoData;

    /**
     * Генерирует SEO данные для страницы каталога
     */
    public function getCatalogSeoData(): SeoData;

    /**
     * Генерирует SEO данные для страницы категории
     */
    public function getCategorySeoData(string $categoryName, string $categorySlug): SeoData;

    /**
     * Генерирует SEO данные для страницы товара
     */
    public function getProductSeoData(string $productName, string $productSlug, ?string $categoryName = null): SeoData;

    /**
     * Генерирует SEO данные для информационных страниц
     */
    public function getPageSeoData(string $pageTitle, string $pageSlug): SeoData;

    /**
     * Генерирует SEO данные для поисковых запросов
     */
    public function getSearchSeoData(string $query): SeoData;
}
