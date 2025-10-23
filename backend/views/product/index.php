<?php

use repositories\Category\models\Category;
use repositories\Product\models\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать товар', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                'filter' => ArrayHelper::map(
                    Category::find()->where(['status' => 1])->all(),
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
            'external_id',
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
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
