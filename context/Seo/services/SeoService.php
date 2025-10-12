<?php

namespace context\Seo\services;

use context\AbstractService;
use context\Seo\config\SeoConfig;
use context\Seo\interfaces\SeoServiceInterface;
use repositories\Seo\models\SeoData;
use yii\web\View;

class SeoService extends AbstractService implements SeoServiceInterface
{
    public function __construct(
        private readonly SeoConfig $seoConfig,
        private readonly View $view
    ) {
    }

    public function setSeoData(SeoData $seoData): void
    {
        // Устанавливаем title
        $this->view->title = $seoData->title;

        // Регистрируем meta теги
        foreach ($seoData->getMetaTags() as $name => $content) {
            $this->view->registerMetaTag(['name' => $name, 'content' => $content]);
        }

        // Регистрируем Open Graph теги
        foreach ($seoData->getOpenGraphTags() as $property => $content) {
            $this->view->registerMetaTag(['property' => $property, 'content' => $content]);
        }

        // Регистрируем Twitter Card теги
        foreach ($seoData->getTwitterCardTags() as $name => $content) {
            $this->view->registerMetaTag(['name' => $name, 'content' => $content]);
        }

        // Регистрируем canonical URL если есть
        if ($seoData->canonicalUrl) {
            $this->view->registerLinkTag(['rel' => 'canonical', 'href' => $seoData->canonicalUrl]);
        }
    }

    public function getHomeSeoData(): SeoData
    {
        $title = $this->seoConfig->siteName;
        $description = $this->seoConfig->siteDescription;
        $keywords = $this->seoConfig->getBaseKeywords();

        $openGraph = [
            'og:url' => $this->seoConfig->siteUrl,
            'og:image' => $this->seoConfig->siteUrl . $this->seoConfig->defaultImageUrl,
            'og:site_name' => $this->seoConfig->siteName,
        ];

        return new SeoData(
            title: $title,
            description: $description,
            keywords: $keywords,
            canonicalUrl: $this->seoConfig->siteUrl,
            openGraph: $openGraph
        );
    }

    public function getCatalogSeoData(): SeoData
    {
        $title = $this->seoConfig->formatTitle('Каталог товаров');
        $description = $this->seoConfig->formatDescription(
            'Полный каталог азиатской кухни. Выберите из широкого ассортимента суши, роллов, лапши и других блюд.'
        );
        
        $keywords = array_merge(
            $this->seoConfig->getBaseKeywords(),
            ['каталог', 'меню', 'блюда', 'ассортимент']
        );

        return new SeoData(
            title: $title,
            description: $description,
            keywords: $keywords
        );
    }

    public function getCategorySeoData(string $categoryName, string $categorySlug): SeoData
    {
        $title = $this->seoConfig->formatTitle($categoryName);
        $description = $this->seoConfig->formatDescription(
            "Заказать {$categoryName} с доставкой в Оренбурге. Свежие продукты, быстрая доставка."
        );
        
        $keywords = array_merge(
            $this->seoConfig->getBaseKeywords(),
            [mb_strtolower($categoryName), 'заказать', 'доставка']
        );

        $canonicalUrl = $this->seoConfig->siteUrl . '/catalog/category/' . $categorySlug;

        return new SeoData(
            title: $title,
            description: $description,
            keywords: $keywords,
            canonicalUrl: $canonicalUrl
        );
    }

    public function getProductSeoData(string $productName, string $productSlug, ?string $categoryName = null): SeoData
    {
        $title = $this->seoConfig->formatTitle($productName);
        
        $description = "Заказать {$productName}";
        if ($categoryName) {
            $description .= " из категории {$categoryName}";
        }
        $description = $this->seoConfig->formatDescription(
            $description . " с доставкой в Оренбурге. Свежие ингредиенты, быстрая доставка."
        );
        
        $keywords = array_merge(
            $this->seoConfig->getBaseKeywords(),
            [mb_strtolower($productName), 'заказать', 'купить']
        );

        if ($categoryName) {
            $keywords[] = mb_strtolower($categoryName);
        }

        $canonicalUrl = $this->seoConfig->siteUrl . '/catalog/product/' . $productSlug;

        return new SeoData(
            title: $title,
            description: $description,
            keywords: $keywords,
            canonicalUrl: $canonicalUrl
        );
    }

    public function getPageSeoData(string $pageTitle, string $pageSlug): SeoData
    {
        $title = $this->seoConfig->formatTitle($pageTitle);
        
        $descriptions = [
            'delivery' => 'Информация о доставке азиатской кухни в Оренбурге. Условия, время доставки, зоны обслуживания.',
            'cooperation' => 'Сотрудничество с Asia Food. Условия для партнеров, поставщиков и франчайзи.',
            'contacts' => 'Контакты Asia Food в Оренбурге. Адрес, телефоны, время работы, способы связи.',
            'about' => 'О ресторане Asia Food. История, миссия, команда. Азиатская кухня в Оренбурге.',
            'privacy-policy' => 'Политика конфиденциальности Asia Food. Обработка персональных данных.',
            'personal-data-consent' => 'Согласие на обработку персональных данных в Asia Food.',
            'advertising-consent' => 'Согласие на получение рекламных материалов от Asia Food.'
        ];

        $description = $descriptions[$pageSlug] ?? 
            $this->seoConfig->formatDescription("Информация - {$pageTitle}");

        $keywords = array_merge(
            [$this->seoConfig->siteName, mb_strtolower($pageTitle)],
            array_slice($this->seoConfig->getBaseKeywords(), 0, 5)
        );

        return new SeoData(
            title: $title,
            description: $description,
            keywords: $keywords
        );
    }

    public function getSearchSeoData(string $query): SeoData
    {
        $title = $this->seoConfig->formatTitle("Поиск: {$query}");
        $description = $this->seoConfig->formatDescription(
            "Результаты поиска по запросу '{$query}' в каталоге азиатской кухни."
        );
        
        $keywords = array_merge(
            $this->seoConfig->getBaseKeywords(),
            ['поиск', mb_strtolower($query)]
        );

        return new SeoData(
            title: $title,
            description: $description,
            keywords: $keywords
        );
    }
}
