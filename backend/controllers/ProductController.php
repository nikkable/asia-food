<?php

namespace backend\controllers;

use repositories\Product\models\Product;
use backend\models\search\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use context\File\interfaces\FileUploadServiceInterface;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Обработка загрузки изображения
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                
                if ($model->imageFile) {
                    $fileUploadService = \Yii::$container->get(FileUploadServiceInterface::class);
                    
                    if ($fileUploadService->isValidImage($model->imageFile)) {
                        $fileName = $fileUploadService->uploadImage($model->imageFile, 'products');
                        if ($fileName) {
                            $model->image = $fileName;
                        } else {
                            $model->addError('imageFile', 'Ошибка при загрузке файла');
                        }
                    } else {
                        $model->addError('imageFile', 'Неверный формат файла. Разрешены: jpg, jpeg, png, gif, webp (макс. 5MB)');
                    }
                }
                
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;

        if ($this->request->isPost && $model->load($this->request->post())) {
            // Обработка загрузки изображения
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->imageFile) {
                $fileUploadService = \Yii::$container->get(FileUploadServiceInterface::class);
                
                if ($fileUploadService->isValidImage($model->imageFile)) {
                    $fileName = $fileUploadService->uploadImage($model->imageFile, 'products', $oldImage);
                    if ($fileName) {
                        $model->image = $fileName;
                    } else {
                        $model->addError('imageFile', 'Ошибка при загрузке файла');
                    }
                } else {
                    $model->addError('imageFile', 'Неверный формат файла. Разрешены: jpg, jpeg, png, gif, webp (макс. 5MB)');
                }
            }
            
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Удаляем изображение если есть
        if ($model->image) {
            $fileUploadService = \Yii::$container->get(FileUploadServiceInterface::class);
            $fileUploadService->deleteFile($model->image, 'products');
        }
        
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
