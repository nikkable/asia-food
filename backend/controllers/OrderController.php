<?php

namespace backend\controllers;

use repositories\Order\models\Order;
use backend\models\search\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
                        'actions' => ['index', 'view', 'update-status', 'cancel'],
                        'allow' => true,
                        'roles' => ['manager'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete', 'update-payment-status'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'cancel' => ['POST'],
                    'update-payment-status' => ['POST'],
                    'update-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * Cancels an order
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        
        // Проверяем, что заказ не был уже отменен
        if ($model->status === Order::STATUS_CANCELLED) {
            \Yii::$app->session->setFlash('warning', 'Заказ #' . $id . ' уже отменен.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Меняем статус на Отменен
        $model->status = Order::STATUS_CANCELLED;
        
        if ($model->save()) {
            \Yii::$app->session->setFlash('success', 'Заказ #' . $id . ' успешно отменен.');
        } else {
            \Yii::$app->session->setFlash('error', 'Ошибка при отмене заказа: ' . implode(', ', $model->getFirstErrors()));
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }
    
    /**
     * Updates payment status of an order
     * @param int $id Order ID
     * @param int $status New payment status
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdatePaymentStatus($id, $status)
    {
        $model = $this->findModel($id);
        
        // Проверяем допустимость статуса
        $allowedStatuses = [
            Order::PAYMENT_STATUS_PENDING,
            Order::PAYMENT_STATUS_PAID,
            Order::PAYMENT_STATUS_FAILED
        ];
        
        if (!in_array($status, $allowedStatuses)) {
            \Yii::$app->session->setFlash('error', 'Недопустимый статус оплаты.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Обновляем статус оплаты
        $model->payment_status = $status;
        // Если оплачен — переводим заказ в статус "Готовится"
        if ((int)$status === Order::PAYMENT_STATUS_PAID) {
            $model->status = Order::STATUS_COOKING;
        }
        
        if ($model->save()) {
            $statusLabels = [
                Order::PAYMENT_STATUS_PENDING => 'Ожидает оплаты',
                Order::PAYMENT_STATUS_PAID => 'Оплачен',
                Order::PAYMENT_STATUS_FAILED => 'Ошибка оплаты'
            ];
            
            $statusLabel = $statusLabels[$status] ?? 'Неизвестный статус';
            \Yii::$app->session->setFlash('success', 'Статус оплаты заказа #' . $id . ' изменен на "' . $statusLabel . '".');
        } else {
            \Yii::$app->session->setFlash('error', 'Ошибка при изменении статуса оплаты: ' . implode(', ', $model->getFirstErrors()));
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }
    
    /**
     * Updates order status
     * @param int $id Order ID
     * @param int $status New status
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateStatus($id, $status)
    {
        $model = $this->findModel($id);
        
        // Проверяем допустимость статуса
        $allowedStatuses = [
            Order::STATUS_NEW,
            Order::STATUS_PROCESSING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
            Order::STATUS_COOKING
        ];
        
        if (!in_array($status, $allowedStatuses)) {
            \Yii::$app->session->setFlash('error', 'Недопустимый статус заказа.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Обновляем статус заказа
        $model->status = $status;
        
        if ($model->save()) {
            $statusLabels = [
                Order::STATUS_NEW => 'Новый',
                Order::STATUS_PROCESSING => 'В обработке',
                Order::STATUS_COMPLETED => 'Выполнен',
                Order::STATUS_CANCELLED => 'Отменен',
                Order::STATUS_COOKING => 'Готовится'
            ];
            
            $statusLabel = $statusLabels[$status] ?? 'Неизвестный статус';
            \Yii::$app->session->setFlash('success', 'Статус заказа #' . $id . ' изменен на "' . $statusLabel . '".');
        } else {
            \Yii::$app->session->setFlash('error', 'Ошибка при изменении статуса заказа: ' . implode(', ', $model->getFirstErrors()));
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
