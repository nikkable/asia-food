<?php

namespace context\Seo\config;

class SeoConfig
{
    public function __construct(
        public readonly string $siteName = 'Asia Food - Азиатская кухня',
        public readonly string $siteDescription = 'Доставка азиатской еды в Оренбурге. Суши, роллы, лапша, рис и многое другое. Быстрая доставка, свежие продукты.',
        public readonly array $defaultKeywords = [
            'азиатская кухня',
            'суши',
            'роллы', 
            'доставка еды',
            'оренбург',
            'японская кухня',
            'китайская кухня',
            'лапша',
            'рис'
        ],
        public readonly string $defaultImageUrl = '/images/logo.png',
        public readonly string $siteUrl = 'https://asia-food.rf',
        public readonly array $socialMedia = [
            'vk' => 'https://vk.com/asiafood_orenburg',
            'telegram' => 'https://t.me/asiafood_orenburg',
            'whatsapp' => 'https://wa.me/79123456789'
        ]
    ) {
    }

    /**
     * Возвращает базовые keywords для всех страниц
     */
    public function getBaseKeywords(): array
    {
        return $this->defaultKeywords;
    }

    /**
     * Генерирует полный title с названием сайта
     */
    public function formatTitle(string $pageTitle): string
    {
        return $pageTitle . ' | ' . $this->siteName;
    }

    /**
     * Генерирует описание с добавлением информации о сайте
     */
    public function formatDescription(string $pageDescription): string
    {
        if (mb_strlen($pageDescription) > 140) {
            return $pageDescription;
        }

        return $pageDescription . ' Заказывайте онлайн с доставкой по Оренбургу.';
    }
}
