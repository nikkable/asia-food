<?php

namespace backend\controllers;

use context\File\models\UploadedFileWrapper;
use repositories\Category\models\Category;
use backend\models\search\CategorySearch;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;
use context\File\interfaces\FileUploadServiceInterface;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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

    public function actionIndex(): string
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionCreate(FileUploadServiceInterface $fileUploadService): Response|string
    {
        $model = new Category();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                
                if ($model->imageFile) {
                    $wrappedFile = new UploadedFileWrapper($model->imageFile);
                    if ($fileUploadService->isValidImage($wrappedFile)) {
                        $fileName = $fileUploadService->uploadImage($wrappedFile, 'categories');
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
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, FileUploadServiceInterface $fileUploadService): Response|string
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->imageFile) {
                $wrappedFile = new UploadedFileWrapper($model->imageFile);
                if ($fileUploadService->isValidImage($wrappedFile)) {
                    $fileName = $fileUploadService->uploadImage($wrappedFile, 'categories', $oldImage);
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
     * @throws \Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id, FileUploadServiceInterface $fileUploadService): Response
    {
        $model = $this->findModel($id);
        
        if ($model->image) {
            $fileUploadService->deleteFile($model->image, 'categories');
        }
        
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Category
    {
        if (($model = Category::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
