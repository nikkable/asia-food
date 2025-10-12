<?php

namespace repositories\Seo\models;

class SeoData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly array $keywords = [],
        public readonly ?string $canonicalUrl = null,
        public readonly array $openGraph = [],
        public readonly array $twitterCard = []
    ) {
    }

    /**
     * Возвращает keywords как строку
     */
    public function getKeywordsString(): string
    {
        return implode(', ', $this->keywords);
    }

    /**
     * Возвращает массив для регистрации meta тегов в Yii2
     */
    public function getMetaTags(): array
    {
        $tags = [
            'description' => $this->description,
        ];

        if (!empty($this->keywords)) {
            $tags['keywords'] = $this->getKeywordsString();
        }

        return $tags;
    }

    /**
     * Возвращает массив Open Graph тегов
     */
    public function getOpenGraphTags(): array
    {
        $defaultOg = [
            'og:title' => $this->title,
            'og:description' => $this->description,
            'og:type' => 'website',
        ];

        return array_merge($defaultOg, $this->openGraph);
    }

    /**
     * Возвращает массив Twitter Card тегов
     */
    public function getTwitterCardTags(): array
    {
        $defaultTwitter = [
            'twitter:card' => 'summary',
            'twitter:title' => $this->title,
            'twitter:description' => $this->description,
        ];

        return array_merge($defaultTwitter, $this->twitterCard);
    }
}
