<?php

use repositories\Product\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'category_id',
                'value' => function ($model) {
                    return $model->category ? $model->category->name : 'Не указана';
                },
                'filter' => \yii\helpers\ArrayHelper::map(
                    \repositories\Category\models\Category::find()->where(['status' => 1])->all(),
                    'id',
                    'name'
                ),
            ],
            'name',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->image) {
                        return Html::img($model->getImageUrl(), [
                            'alt' => Html::encode($model->name),
                            'style' => 'max-width: 50px; max-height: 50px;'
                        ]);
                    }
                    return 'Нет';
                },
                'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
            ],
            'price:currency',
            'quantity',
            'article',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $class = $model->status ? 'label-success' : 'label-default';
                    $text = $model->status ? 'Активный' : 'Неактивный';
                    return "<span class='label {$class}'>{$text}</span>";
                },
                'filter' => [
                    1 => 'Активный',
                    0 => 'Неактивный'
                ],
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
