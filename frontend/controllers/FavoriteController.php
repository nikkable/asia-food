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
    
    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param FavoriteServiceInterface $favoriteService
     * @param ProductServiceInterface $productService
     * @param array $config
     */
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
    
    /**
     * {@inheritdoc}
     */
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
    
    /**
     * Отображает список избранных товаров
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        $favorites = $this->favoriteService->getFavorites();
        
        return $this->render('index', [
            'favorites' => $favorites,
        ]);
    }
    
    /**
     * Добавляет товар в избранное
     * 
     * @param int $id ID товара
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionAdd($id)
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
        return $this->redirect(['index']);
    }
    
    /**
     * Удаляет товар из избранного
     * 
     * @return Response
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
            return $this->redirect(['index']);
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
     * 
     * @return Response
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
     * 
     * @param int $id ID товара
     * @return Response
     */
    public function actionCheck($id)
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
     * 
     * @return string
     */
    public function actionModalContent()
    {
        $favorites = $this->favoriteService->getFavorites();
        
        return $this->renderPartial('_modal_content', [
            'favorites' => $favorites,
        ]);
    }
}
