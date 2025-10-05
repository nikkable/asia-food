<?php

namespace repositories\Favorite;

use repositories\Favorite\interfaces\FavoriteRepositoryInterface;
use repositories\Favorite\models\Favorite;
use repositories\Favorite\models\FavoriteList;
use repositories\Product\interfaces\ProductRepositoryInterface;
use Yii;

/**
 * Репозиторий для работы с избранными товарами
 * Хранит избранное в сессии пользователя
 */
class FavoriteRepository implements FavoriteRepositoryInterface
{
    private const SESSION_KEY = 'favorite';
    
    private ProductRepositoryInterface $productRepository;
    
    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        $session = Yii::$app->session;
        $favoriteIds = $session->get(self::SESSION_KEY, []);
        
        $items = [];
        foreach ($favoriteIds as $productId) {
            $product = $this->productRepository->findById($productId);
            if ($product) {
                $items[$productId] = new Favorite($productId, $product);
            }
        }
        
        return $items;
    }
    
    /**
     * {@inheritdoc}
     */
    public function add(int $productId): bool
    {
        $session = Yii::$app->session;
        $favoriteIds = $session->get(self::SESSION_KEY, []);
        
        if (!in_array($productId, $favoriteIds)) {
            $favoriteIds[] = $productId;
            $session->set(self::SESSION_KEY, $favoriteIds);
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove(int $productId): bool
    {
        $session = Yii::$app->session;
        $favoriteIds = $session->get(self::SESSION_KEY, []);
        
        $key = array_search($productId, $favoriteIds);
        if ($key !== false) {
            unset($favoriteIds[$key]);
            $session->set(self::SESSION_KEY, array_values($favoriteIds));
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function exists(int $productId): bool
    {
        $session = Yii::$app->session;
        $favoriteIds = $session->get(self::SESSION_KEY, []);
        
        return in_array($productId, $favoriteIds);
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        $session = Yii::$app->session;
        $session->remove(self::SESSION_KEY);
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCount(): int
    {
        $session = Yii::$app->session;
        $favoriteIds = $session->get(self::SESSION_KEY, []);
        
        return count($favoriteIds);
    }
}
