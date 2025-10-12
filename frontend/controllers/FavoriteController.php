<?php

namespace frontend\controllers;

use context\Favorite\interfaces\FavoriteServiceInterface;
use context\Product\interfaces\ProductServiceInterface;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * FavoriteController реализует действия для работы с избранными товарами
 */
class FavoriteController extends Controller
{
    private $favoriteService;
    private $productService;
    
    public function __construct(
        $id, 
        $module, 
        FavoriteServiceInterface $favoriteService, 
        ProductServiceInterface $productService, 
        $config = []
    ) {
        $this->favoriteService = $favoriteService;
        $this->productService = $productService;
        parent::__construct($id, $module, $config);
    }
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add' => ['POST'],
                    'remove' => ['POST'],
                    'clear' => ['POST'],
                ],
            ],
        ];
    }
    
    public function actionIndex(): Response
    {
        return $this->redirect(['/site/index']);
    }
    
    /**
     * Добавляет товар в избранное
     * @throws NotFoundHttpException
     */
    public function actionAdd(int $id)
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            
            $success = $this->favoriteService->addProduct($id);
            
            if ($success) {
                $favorites = $this->favoriteService->getFavorites();
                return [
                    'success' => true,
                    'message' => 'Товар добавлен в избранное',
                    'favoritesCount' => $favorites->getCount(),
                    'productId' => $id,
                    'isInFavorites' => true
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Не удалось добавить товар в избранное'
                ];
            }
        }
        
        $product = $this->productService->findById($id);
        if (!$product) {
            throw new NotFoundHttpException('Товар не найден');
        }
        
        $this->favoriteService->addProduct($id);
        return $this->redirect(['/site/index']);
    }
    
    /**
     * Удаляет товар из избранного
     */
    public function actionRemove()
    {
        // Получаем ID из GET или POST параметров
        $id = \Yii::$app->request->get('id');
        if (!$id) {
            $id = \Yii::$app->request->post('id');
        }
        
        if (!$id) {
            if (\Yii::$app->request->isAjax) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Не указан ID товара'
                ];
            }
            return $this->redirect(['/site/index']);
        }
        
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            
            $success = $this->favoriteService->removeProduct($id);
            $favorites = $this->favoriteService->getFavorites();
            
            return [
                'success' => $success,
                'message' => $success ? 'Товар удален из избранного' : 'Не удалось удалить товар из избранного',
                'favoritesCount' => $favorites->getCount(),
                'removedProductId' => $id,
                'isInFavorites' => false
            ];
        }
        
        $this->favoriteService->removeProduct($id);
        return $this->redirect(['index']);
    }
    
    /**
     * Очищает список избранных товаров
     */
    public function actionClear()
    {
        $success = $this->favoriteService->clearFavorites();
        
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => $success,
                'message' => $success ? 'Список избранного очищен' : 'Не удалось очистить список избранного',
                'favoritesCount' => 0
            ];
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * Проверяет, находится ли товар в избранном
     */
    public function actionCheck(int $id): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        
        $isInFavorites = $this->favoriteService->isInFavorites($id);
        
        return [
            'success' => true,
            'isInFavorites' => $isInFavorites,
            'productId' => $id
        ];
    }
    
    /**
     * Возвращает HTML-содержимое модального окна избранного
     */
    public function actionModalContent() :string
    {
        $favorites = $this->favoriteService->getFavorites();
        
        return $this->renderPartial('_modal_content', [
            'favorites' => $favorites,
        ]);
    }
}
