<?php

namespace frontend\controllers;

use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * Контроллер личного кабинета пользователя
 */
class ProfileController extends Controller
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        $id,
        $module,
        OrderRepositoryInterface $orderRepository,
        $config = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Только для авторизованных пользователей
                    ],
                ],
            ],
        ];
    }

    /**
     * Главная страница профиля
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        // Получаем последние заказы пользователя
        $recentOrders = Order::find()
            ->where(['user_id' => $user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();
        
        // Статистика пользователя
        $totalOrders = Order::find()->where(['user_id' => $user->id])->count();
        $totalSpent = Order::find()
            ->where(['user_id' => $user->id, 'payment_status' => Order::PAYMENT_STATUS_PAID])
            ->sum('total_cost') ?: 0;
        
        return $this->render('index', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent,
        ]);
    }

    /**
     * Страница редактирования профиля
     */
    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        
        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->setFlash('success', 'Профиль успешно обновлен');
            return $this->redirect(['index']);
        }
        
        return $this->render('edit', [
            'user' => $user,
        ]);
    }

    /**
     * История заказов пользователя
     */
    public function actionOrders()
    {
        $user = Yii::$app->user->identity;
        
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()
                ->where(['user_id' => $user->id])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        return $this->render('orders', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
